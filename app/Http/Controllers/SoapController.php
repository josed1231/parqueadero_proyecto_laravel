<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Concerns\ValidaTokenSoap;
use App\Models\Espacio;
use App\Models\Vehiculo;
use App\Models\Ingreso;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use SoapServer;
use SoapFault;

class SoapController extends Controller
{
    use ValidaTokenSoap;

    protected $usuarioAutenticado = null;

    public function server(Request $request)
    {
        $wsdlPath = public_path('wsdl/parqueadero.wsdl');

        if ($request->has('wsdl')) {
            if (!file_exists($wsdlPath)) {
                return response('El archivo WSDL no existe en public/wsdl/parqueadero.wsdl', 404);
            }
            return response()->file($wsdlPath, [
                'Content-Type' => 'application/xml; charset=utf-8'
            ]);
        }

        // Si la petición NO es LoginService, validamos token y permisos de rol
        $xmlContent = $request->getContent();
        if (!str_contains($xmlContent, 'LoginService')) {
            try {
                $this->usuarioAutenticado = $this->validarTokenDesdeSoap($request);
            } catch (SoapFault $e) {
                return $this->generarSoapFault($e->getMessage());
            }

            $rol = $this->usuarioAutenticado->role ?? null;

            // El perfil cajero no tiene permiso para consumir ningún método SOAP
            if ($rol === 'cajero') {
                return response()->json([
                    'error' => 'Permisos insuficientes para ejecutar este método'
                ], 403);
            }

            // Validación específica para consultarIngresos()
            if (str_contains($xmlContent, 'consultarIngresos')) {
                $rolesPermitidos = ['administrador', 'operador_cabina', 'supervisor_salida'];
                if (!in_array($rol, $rolesPermitidos)) {
                    return response()->json([
                        'error' => 'Permisos insuficientes para ejecutar este método'
                    ], 403);
                }
            }

            // Operaciones administrativas de espacios reservadas solo al administrador
            if (str_contains($xmlContent, 'registrarEspacio') || str_contains($xmlContent, 'actualizarEspacio')) {
                if ($rol !== 'administrador') {
                    return response()->json([
                        'error' => 'Permisos insuficientes para ejecutar este método'
                    ], 403);
                }
            }
        }

        ini_set('soap.wsdl_cache_enabled', 0);
        $server = new SoapServer($wsdlPath, [
            'uri' => url('/api/soap'),
        ]);

        $server->setObject($this);

        ob_start();
        $server->handle();
        $response = ob_get_clean();

        return response($response, 200, [
            'Content-Type' => 'text/xml; charset=utf-8'
        ]);
    }

    // --- MÉTODOS DEL SERVICIO SOAP ---

    public function LoginService($email, $password)
    {
        $user = User::where('email', $email)->first();
        if (!$user || !Hash::check($password, $user->password)) {
            throw new SoapFault('Client', 'Credenciales inválidas.');
        }

        $token = $user->createToken('soap_token')->plainTextToken;
        return json_encode([
            'status' => 'success',
            'token'  => $token,
            'user'   => $user->name,
            'role'   => $user->role,
        ]);
    }

    public function registrarEspacio($numero, $tipo, $estado)
    {
        $tarifaBase = (strtolower($tipo) === 'moto') ? 1000 : 2000;

        $espacio = Espacio::create([
            'codigo' => $numero,
            'tipo'   => ucfirst(strtolower($tipo)),
            'tarifa' => $tarifaBase,
            'estado' => $estado ? ucfirst(strtolower($estado)) : 'Disponible',
        ]);

        return json_encode($espacio);
    }

    public function actualizarEspacio($id, $estado)
    {
        $espacio = Espacio::findOrFail($id);
        $espacio->update([
            'estado' => ucfirst(strtolower($estado))
        ]);

        return json_encode($espacio);
    }

    public function consultarEspacio($id)
    {
        $espacio = Espacio::findOrFail($id);
        return json_encode($espacio);
    }

    public function listarEspacios()
    {
        return json_encode(Espacio::all());
    }

    public function registrarIngreso($placa, $tipo_vehiculo, $espacio_id)
    {
        $vehiculo = Vehiculo::firstOrCreate(
            ['placa' => strtoupper($placa)],
            [
                'tipo'    => ucfirst(strtolower($tipo_vehiculo)),
                'user_id' => $this->usuarioAutenticado ? $this->usuarioAutenticado->id : 1
            ]
        );

        $espacio = Espacio::findOrFail($espacio_id);
        $espacio->update(['estado' => 'Ocupado']);

        $ingreso = Ingreso::create([
            'espacio_id'   => $espacio->id,
            'vehiculo_id'  => $vehiculo->id,
            'fechaEntrada' => now(),
            'estado'       => 'Activo'
        ]);

        return json_encode($ingreso->load(['vehiculo', 'espacio']));
    }

    public function consultarIngresos()
    {
        return json_encode(Ingreso::with(['vehiculo', 'espacio', 'factura'])->get());
    }

    private function generarSoapFault($mensaje)
    {
        $faultXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
    <soapenv:Body>
        <soapenv:Fault>
            <faultcode>soapenv:Client.Authentication</faultcode>
            <faultstring>{$mensaje}</faultstring>
        </soapenv:Fault>
    </soapenv:Body>
</soapenv:Envelope>
XML;
        return response($faultXml, 401, ['Content-Type' => 'text/xml; charset=utf-8']);
    }
}