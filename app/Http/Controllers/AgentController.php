<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AgentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $agents = Agent::all();
        return response()->json($agents, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Agent  $agent
     * @return \Illuminate\Http\Response
     */
    public function show(Agent $agent)
    {
        return response()->json($agent, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Agent  $agent
     * @return \Illuminate\Http\Response
     */
    public function edit(Agent $agent)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Agent  $agent
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $agent = Agent::where('agentUser', $id)->first();

        if (!$agent) {
            return response()->json([
                'status' => 'error',
                'message' => 'Agent not found'
            ], 404);
        }

        // Validamos los datos

        if ($request->email != $agent->email) {
            $validator = Validator::make($request->all(), [
                'email' => 'unique:agents',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();

                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid data provided',
                    'errors' => $errors
                ], 422);
            }
        }


        if ($request->name) {
            $agent->name = $request->name;
        }

        if ($request->email) {
            $agent->email = $request->email;
        }

        if ($request->password) {
            $agent->password = bcrypt($request->password);
        }

        if ($request->agentStatus) {
            $agent->agentStatus = $request->agentStatus;
        }


        $agent->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Agent updated successfully'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Agent  $agent
     * @return \Illuminate\Http\Response
     */
    public function destroy(Agent $agent)
    {
        // Eliminamos el agente de la base de datos
        $agent->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Agent deleted successfully'
        ], 200);
    }

    /**
     * Crea un agentUser aleatoreo, validando que este no exista antes en la base de datos
     * debe ser unico para el agente, no puede haber dos agentes con el mismo agentUser
     */
    public function creategentUser()
    {

        $agentUser = $this->generateAgentUser();
        $agent = Agent::where('agentUser', $agentUser)->first();
        while ($agent != null) {
            $agentUser = $this->generateAgentUser();
            $agent = Agent::where('agentUser', $agentUser)->first();
        }
        return $agentUser;
    }

    /**
     * Genera un agentUser aleatoreo
     */
    function generateAgentUser()
    {
        $agentUser = "";
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        for ($i = 0; $i < 8; $i++) {
            $agentUser .= $characters[rand(0, $charactersLength - 1)];
        }
        return $agentUser;
    }

    public function delete($id)
    {
        $agent = Agent::where('agentUser', $id)->first();

        if (!$agent) {
            return response()->json([
                'status' => 'error',
                'message' => 'Agent not found'
            ], 404);
        }
        return $this->destroy($agent);
    }
}
