<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class general extends CI_Model
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->model('/bo/bonos/calculador_bono');
		$this->load->model('/bo/bonos/afiliado');
	
	}
	
	function IsActivedPago($id){
		$q = $this->db->query('select estatus from user_profiles where user_id = '.$id);
		$estado = $q->result();
	
		if($estado[0]->estatus == 'ACT'){
			return true;
		}else{
			if($this->VerificarCompraPaquete($id)){
				$this->actualizarEstadoAfiliado($id);
				return true;
			}else{
				return false;
			}
		}
	}
	
	function actualizarEstadoAfiliado($id){
		$q = $this->db->query("UPDATE user_profiles SET estatus = 'ACT' WHERE user_id = ".$id);
	}
	
	function VerificarCompraPaquete($id){
		$q = $this->db->query("SELECT m.id FROM cross_venta_mercancia cvm, venta v, mercancia m
		where v.id_estatus = 2 and cvm.id_venta = v.id_venta and cvm.id_mercancia = m.id and m.id_tipo_mercancia = 4 and v.id_user = ".$id);
	
		$mercnacias_id = $q->result();
	
		if(isset($mercnacias_id[0]->id)){
			return true;
		}else{
			return false;
		}
	}
	
	function isAValidUser($id,$modulo){
	
		$q=$this->db->query('SELECT cu.id_tipo_usuario as tipoId
							FROM users u , user_profiles up ,cat_tipo_usuario cu
							where(u.id=up.user_id)
							and (up.id_tipo_usuario=cu.id_tipo_usuario)
							and(u.id='.$id.')');
		$tipo=$q->result();
	
		$idTipoUsuario=$tipo[0]->tipoId;
		
		if($modulo=="OV"){
			return $this->IsActivedPago($id);
			
		}else if($modulo=="comercial"){
			if($idTipoUsuario==4||$idTipoUsuario==1)
				return true;
			return false;
		}else if($modulo=="soporte"){
			if($idTipoUsuario==3||$idTipoUsuario==1)
				return true;
			return false;
		}else if($modulo=="logistica"){
			if($idTipoUsuario==5||$idTipoUsuario==1)
				return true;
			return false;
		}else if($modulo=="oficina"){
			if($idTipoUsuario==6||$idTipoUsuario==1)
				return true;
			return false;
		}else if($modulo=="administracion"){
			if($idTipoUsuario==7||$idTipoUsuario==1)
				return true;
			return false;
		}
		return false;
	}
	
	function isActived($id){
	
		if($id==2)
			return 0;
		
		return $this->validarMembresias($id);

	}
	
	function isActivedAfiliacionesPuntosPersonales($id_afiliado,$fecha){
	$cualquiera="0";
		
	$numeroAfiliadosDirectos=$this->getAfiliadosDirectos($id_afiliado);
	$afiliadosParaEstarActivo=$this->getAfiliadosParaEstarActivo();
	
	if ($afiliadosParaEstarActivo > $numeroAfiliadosDirectos) {
            $this->Activacion($id_afiliado,'DES'); 
            return false;
        }

        $fechaInicio=$this->calculador_bono->getInicioMes($fecha);
	$fechaFin=$this->calculador_bono->getFinMes($fecha);
	
	$puntosParaEstarActivo=$this->getPuntosParaEstarActivo();
	$puntosComisionablesMes=0;
	
	$q=$this->db->query('select id from tipo_red');
	$redes= $q->result();
	
	foreach ($redes as $red){
		$puntos=$this->afiliado->getPuntosTotalesPersonalesIntervalosDeTiempo($id_afiliado,$red->id,$cualquiera,$cualquiera,$fechaInicio,$fechaFin)[0]->total;
		$puntosComisionablesMes=$puntosComisionablesMes+$puntos;
		
	}
	
	if ($puntosParaEstarActivo > $puntosComisionablesMes) {
            $this->Activacion($id_afiliado,'DES'); 
            return false;
        }


            return true;
	}
	
	private function getAfiliadosDirectos($id_afiliado){
		$q=$this->db->query('SELECT count(*) as directos FROM users u,afiliar a
		where u.id=a.id_afiliado and a.directo = '.$id_afiliado); 
		$numeroAfiliados=$q->result();
		
		if($numeroAfiliados[0]->directos==null)
			return 0;
		
		return $numeroAfiliados[0]->directos;
		
	}
	
	private function getAfiliadosParaEstarActivo(){
		$q=$this->db->query('SELECT afiliados_directos as directos FROM empresa_multinivel');
		$afiliadosDirectos=$q->result();
		
		if($afiliadosDirectos[0]->directos==null)
			return 0;
		
		return $afiliadosDirectos[0]->directos;
	}
	
	private function getPuntosParaEstarActivo(){
		$q=$this->db->query('SELECT puntos_personales as puntos FROM empresa_multinivel');
		$afiliadosDirectos=$q->result();
	
		if($afiliadosDirectos[0]->puntos==null)
			return 0;
	
		return $afiliadosDirectos[0]->puntos;
	}
	
	private function validarMembresias($id){
		$membresia=1;
		
		if($this->compraObligatoria ($membresia)&&$this->hayTipoDeMercancia ($membresia)){
			if($this->compraDeUsuarioEstaActiva($membresia,$id)){
				// validar Paquetes
				return $this->validarPaqueteInscripcion($id);
			}
			else{
				//Mostrar Membresias
                                $this->Activacion($id,'DES'); 
				return 1;
			}
		}else {
			//validarPaquetes
			
			return $this->validarPaqueteInscripcion($id);
		
		}
	}
	
	private function validarPaqueteInscripcion($id){
		$paqueteDeInscripcion=2;
	
		if($this->compraObligatoria ($paqueteDeInscripcion)&&$this->hayTipoDeMercancia ($paqueteDeInscripcion)){
			if($this->compraDeUsuarioEstaActiva($paqueteDeInscripcion,$id)){
					// validar Items
				return $this->validarItems($id);
			}
			else{
				//Mostrar Paquetes
                                $this->Activacion($id,'DES'); 
				return 2;
			}
		}else {
			// validar Items
			return $this->validarItems($id);
		}
	}
	
	private function validarItems($id){
		$item=3;
	
		if($this->compraObligatoria ($item)&&$this->hayTipoDeMercancia ($item)){
			if($this->compraDeUsuarioEstaActiva($item,$id)){
				// Acceso
                                $this->Activacion($id,'ACT');    
				return 0;
			}
			else{
				//Mostrar Item
                                $this->Activacion($id,'DES'); 
				return 3;
			}
		}else {
			// Acceso
			return 0;
		}
	}
	
	
	private function compraObligatoria($id_tipo_mercancia) {
	 
		if($id_tipo_mercancia == 1){
			$q = $this->db->query("SELECT membresia as estado FROM empresa_multinivel;");
		}elseif ($id_tipo_mercancia == 2){
			$q = $this->db->query("SELECT paquete as estado FROM empresa_multinivel;");
		}elseif($id_tipo_mercancia == 3) {
			$q = $this->db->query("SELECT item as estado FROM empresa_multinivel;");
		}else{
			return false;
		}
		$estado=$q->result();
		
		if($estado[0]->estado=='ACT')
			return true;
		
		
		return false;

	}
	
	private function hayTipoDeMercancia($id_tipo_mercancia) {
	
		if($id_tipo_mercancia == 1){
			$q = $this->db->query("SELECT * FROM mercancia where id_tipo_mercancia=5");
		}elseif ($id_tipo_mercancia == 2){
			$q = $this->db->query("SELECT * FROM mercancia where id_tipo_mercancia=4");
		}elseif($id_tipo_mercancia == 3) {
			$q = $this->db->query("SELECT * FROM mercancia where id_tipo_mercancia=1 or id_tipo_mercancia=2 or id_tipo_mercancia=3");
		}else{
			return false;
		}

		$mercancia=$q->result();
		
		if($mercancia)
			return true;

		return false;
	
	}

	
	private function compraDeUsuarioEstaActiva($id_tipo_mercancia,$id) {
	
		if($id_tipo_mercancia == 1){
			$q = $this->db->query("SELECT v.id_venta,v.fecha,me.caducidad,DATEDIFF(now(),v.fecha)as dias_activacion FROM venta v,cross_venta_mercancia cvm,mercancia m,membresia me
									where v.id_estatus='ACT'
									and v.id_venta=cvm.id_venta
									and m.id=cvm.id_mercancia
									and m.id_tipo_mercancia=5
									and v.id_user='".$id."'
									and m.sku=me.id
									and (DATEDIFF(now(),v.fecha)<=me.caducidad or me.caducidad=0)");
		}elseif ($id_tipo_mercancia == 2){
			$q = $this->db->query("SELECT v.id_venta,v.fecha,pa.caducidad,DATEDIFF(now(),v.fecha)as dias_activacion FROM venta v,cross_venta_mercancia cvm,mercancia m,paquete_inscripcion pa
									where v.id_estatus='ACT'
									and v.id_venta=cvm.id_venta
									and m.id=cvm.id_mercancia
									and m.id_tipo_mercancia=4
									and v.id_user='".$id."'
									and m.sku=pa.id_paquete
									and (DATEDIFF(now(),v.fecha)<=pa.caducidad or pa.caducidad=0)");
		}elseif($id_tipo_mercancia == 3) {
			$q = $this->db->query("SELECT v.id_venta,v.fecha FROM venta v,cross_venta_mercancia cvm,mercancia m
									where v.id_estatus='ACT'
									and v.id_venta=cvm.id_venta
									and m.id=cvm.id_mercancia
									and (m.id_tipo_mercancia!=4)
									and (m.id_tipo_mercancia!=5)
									and v.id_user='".$id."'");
		}else{
			return false;
		}
	
		$activacion=$q->result();
	
		if($activacion)
			return true;
	
		return false;
	
	}
	
	function get_username($id)
	{
		$q=$this->db->query('select * from user_profiles where user_id = "'.$id.'"');
		return $q->result();
	}
	function get_style($id)
	{
	  	$q=$this->db->query('select * from estilo_usuario where id_usuario = '.$id);
	 	return $q->result();
	}

	function get_email($id)
	{
		$q=$this->db->query('select email from users where id = '.$id);
		return $q->result();
	}
	
	function get_pais($id)
	{
		$q=$this->db->query("select cu.pais as pais,c.Name as nombrePais,c.Code2 as codigo,concat(cu.calle,' ',cu.colonia,' ',cu.municipio,' ',cu.estado)as direccion
								,cu.cp as codigo_postal,cu.estado as estado,cu.municipio as municipio,cu.colonia as colonia,cu.calle as calle
								from cross_dir_user cu,Country c
								where c.Code=cu.pais and cu.id_user = ".$id."");
		return $q->result();
	}
	
	function username($id)
	{
		$q=$this->db->query('select username from users where id = '.$id);
		return $q->result();
	}
	function emailPagos()
	{
		$q=$this->db->query(' SELECT email FROM emails_departamentos LIMIT 0 , 1');
		return $q->result();
	}
	
	function setArrayVarchar($array){ 
		$ArrayVarchar = array();
		foreach ($array as $key){
			if(!preg_match('/^[0-9]{1,}$/', $key)){
				$key = '\''.$key.'\'';
			}
			array_push($ArrayVarchar, $key);
		}
		return implode(',',$ArrayVarchar);
	}

    public function Activacion($id, $estatus) {
        
        $this->db->query("UPDATE user_profiles SET activacion = '".$estatus."' WHERE user_id  = ".$id);
        
        return true;
    }
    
    public function getActivacion($id) {
        
        $q=$this->db->query("SELECT activacion FROM user_profiles WHERE user_id  = ".$id);
        $q=$q->result();
        return $q[0]->activacion;
        
    }
    
    function viewTable($datos){
        
        //var_dump($datos);
        if(!$datos){
            return "";
        }
                
        $table = "<div style='overflow-y: scroll; height: 100px;'><table id='datatable_fixed_column1' class='table table-striped table-bordered table-hover' width='80%'>
				<thead id='tablacabeza'>";
        $i=0;
        foreach ($datos[0] as $key => $value){
            $class= ($i==0) ? "data-class='expand'" : "data-hide='phone,tablet'";
            $table.="<th ".$class." >".strtoupper($key)."</th>";
            //echo strtoupper($key)."|";
            $i++;
        }        
        $table .= " </thead>
		<tbody>";
	
	
		foreach($datos as $dato)
		{
                    $table .= "<tr>";
                    $i=0;
                    foreach ($dato as $key => $value){
                        $class= ($i==0) ? "class='sorting_1'" : "";
                        $table .= "<td ".$class." >".$value."</td>";
                        //echo strtoupper($key).":".$value."|";
                        $i++;
                        //$total += ($bono->valor);
                    }
                    $table .= "</tr>";
		}
			
		/*$bonos_table .= "<tr>
			<td class='sorting_1'></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			</tr>";
	
		$bonos_table .= "<tr>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td class='sorting_1'><b>TOTAL:</b></td>
			<td><b> $	".number_format($total, 2)."</b></td>
			</tr>";*/
	
		$table .= "</tbody>
		</table><tr class='odd' role='row'></div>";
		
                //escribir funcion js
                /*$table.=  "<script>"
                                .   "function profundizar(id){"
                                .   "   $.ajax({
                                            type: 'POST',
                                            url: '/ov/billetera2/profundizar_bono',
                                            data: {id: id}
                                        }).done(function( msg ){					
                                                bootbox.dialog({
                                                    message: msg,
                                                    title: 'Detalles del Bono',
                                                    buttons: {
                                                        danger: {
                                                            label: 'Cerrar',
                                                            className: 'btn-danger',
                                                            callback: function() {

                                                            }
                                                        }
                                                    }
                                                })//fin done ajax
                                            });//Fin callback bootbox
                                        }"
                               ."</script>";*/
                
		return $table;
    }

}
