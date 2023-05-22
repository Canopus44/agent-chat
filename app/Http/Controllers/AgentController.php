<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //
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
    public function update(Request $request, Agent $agent)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Agent  $agent
     * @return \Illuminate\Http\Response
     */
    public function destroy(Agent $agent)
    {
        //
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
}
