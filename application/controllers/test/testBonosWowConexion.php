<?php
class testBonosWowConexion extends CI_Controller {

	private $idBonoDeEquipo=56;
	private $idBonoDePatrocinio=57;
	private $idBonoWinner=57;

	public function __construct() {
		parent::__construct();
		$this->load->library('unit_test');
		$this->load->model('/bo/bonos/activacion_bono');
		$this->load->model('/bo/bonos/afiliado');
		$this->load->model('/bo/bonos/bono');
		$this->load->model('/bo/bonos/calculador_bono');
		$this->load->model('/bo/bonos/condiciones_bono');
		$this->load->model('/bo/bonos/mercancia');
		$this->load->model('/bo/bonos/red');
		$this->load->model('/bo/bonos/valores_bono');
		$this->load->model('/bo/bonos/venta');
		$this->load->model('/bo/bonos/modelo_bono');
	//	$this->load->model('/bo/bonos/clientes/mobileMoney/bono_wow_conexion');

	}

	private function before(){
		$this->modelo_bono->limpiarTodosLosBonos();
		$this->afiliado->eliminarUsuarios();
		$this->afiliado->eliminarRemanentes();
		$this->red->eliminarRed();
		$this->mercancia->eliminarMercancias();
		$this->mercancia->eliminarCategorias();
		$this->venta->eliminarVentas();
		$this->repartidor_comision_bono->eliminarHistorialComisionBono();
		$this->ingresarBonos();
		$this->ingresarRedDeAfiliacion();
		$this->ingresarVentasFecha(date('Y-m-d'),true,700);
	}
	
	private function after(){
		$this->modelo_bono->limpiarTodosLosBonos();
		$this->afiliado->eliminarUsuarios();
		$this->afiliado->eliminarRemanentes();
		$this->red->eliminarRed();
		$this->mercancia->eliminarMercancias();
		$this->mercancia->eliminarCategorias();
		$this->venta->eliminarVentas();
		$this->repartidor_comision_bono->eliminarHistorialComisionBono();

	}
	
	public function index(){

    	$this->before();
    	$this->testCalcularComisionesAfiliadosBonoDeEquipo();

	/*	$this->pruebaProduccion();
		
		$this->testValidarSiElBonoYaCobroFalso();
		$this->after();

		$this->before();
		$this->testCalcularComisionesAfiliadosBonoBinario();
		$this->testGetTotalDeDineroARepartir();
		$this->testARecibir();
		$this->testRepartirComisiones();
		$this->testValidarSiElBonoYaCobroVerdadero($this->idBonoDeBinario);
		
		$this->before();
		$this->testCalcularComisionesAfiliadosBonoDeInicioRapido();
		$this->testValidarSiElBonoYaCobroVerdadero($this->idBonoDeEquipo);
		$this->after(); */

	}
	
	public function pruebaProduccion(){
		$this->ingresarBonos();

	}

	public function testValidarSiElBonoYaCobroFalso(){
		$this->repartidor_comision_bono->eliminarHistorialComisionBono();
		
		$fechaActual=date('Y-m-d');
		
		$calculadorBono=new $this->calculador_bono();

		$id_bono=$this->idBonoDeEquipo;$bono=new $this->bono();$bono->setUpBono($id_bono);
		$resultado=$calculadorBono->isPagado($bono,$fechaActual);
		echo $this->unit->run(false,$resultado, 'Test validar si el bono no sea pagado','Resultado es :'.$resultado);

		$id_bono=$this->idBonoDeBinario;$bono=new $this->bono();$bono->setUpBono($id_bono);
		$resultado=$calculadorBono->isPagado($bono,$fechaActual);
		echo $this->unit->run(false,$resultado, 'Test validar si el bono no sea pagado','Resultado es :'.$resultado);
		
	}

	public function testCalcularComisionesAfiliadosBonoDeEquipo(){
		$this->repartidor_comision_bono->eliminarHistorialComisionBono();
		$this->afiliado->eliminarRemanentes();
		$repartidorComisionBono=new$this->repartidor_comision_bono();
		
		$fecha=date('Y-m-d');
		$id_bono=$this->idBonoDeEquipo;
		
		$calculadorBono=new $this->calculador_bono();
		$calculadorBono->calcularComisionesPorBono($id_bono,$fecha);

		//BONO De Inicio Rapido

		$id_usuario=10000;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		$resultadoEsperado=79710;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10001;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		$resultadoEsperado=37635;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10002;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);

		$id_usuario=10003;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);

		$id_usuario=10004;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10005;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10006;
		$resultadoEsperado=24960;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10007;
		$resultadoEsperado=33300;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10008;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10009;
		$resultadoEsperado=30800;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10010;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);

		$id_usuario=10011;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10012;
		$resultadoEsperado=41750;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10013;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10014;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);

		$id_usuario=10015;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10016;
		$resultadoEsperado=33100;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);

		$id_usuario=10017;
		$resultadoEsperado=4700;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10018;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10019;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10020;
		$resultadoEsperado=700;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);

		$id_usuario=10021;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10022;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);

		$id_usuario=10023;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10024;
		$resultadoEsperado=16500;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10025;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10026;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10027;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);

		$id_usuario=10028;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10029;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10030;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10031;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10032;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);

		$id_usuario=10033;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10034;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10035;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10036;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
	}

	public function testCalcularComisionesAfiliadosBonoDePatrocinio(){
		$this->repartidor_comision_bono->eliminarHistorialComisionBono();
		$this->afiliado->eliminarRemanentes();
		$repartidorComisionBono=new$this->repartidor_comision_bono();
		
		$fecha=date('Y-m-d');
		$id_bono=$this->idBonoDePatrocinio;
		
		$calculadorBono=new $this->calculador_bono();
		$calculadorBono->calcularComisionesPorBono($id_bono,$fecha);

		//BONO De Patrocinio

		$id_usuario=10000;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		$resultadoEsperado=79710;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10001;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		$resultadoEsperado=37635;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10002;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);

		$id_usuario=10003;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);

		$id_usuario=10004;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10005;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10006;
		$resultadoEsperado=24960;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10007;
		$resultadoEsperado=33300;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10008;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10009;
		$resultadoEsperado=30800;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10010;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);

		$id_usuario=10011;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10012;
		$resultadoEsperado=41750;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10013;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10014;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);

		$id_usuario=10015;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10016;
		$resultadoEsperado=33100;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);

		$id_usuario=10017;
		$resultadoEsperado=4700;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10018;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10019;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10020;
		$resultadoEsperado=700;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);

		$id_usuario=10021;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10022;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);

		$id_usuario=10023;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10024;
		$resultadoEsperado=16500;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10025;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10026;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10027;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);

		$id_usuario=10028;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10029;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10030;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10031;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10032;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);

		$id_usuario=10033;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10034;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10035;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
		$id_usuario=10036;
		$resultadoEsperado=0;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run($resultadoEsperado,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado." deberia ser : ".$resultadoEsperado);
		
	}

	public function testCalcularComisionesAfiliadosBonoWinner(){
		$this->modelo_bono->limpiarTodosLosBonos();
		$this->afiliado->eliminarUsuarios();
		$this->afiliado->eliminarRemanentes();
		$this->red->eliminarRed();
		$this->mercancia->eliminarMercancias();
		$this->mercancia->eliminarCategorias();
		$this->venta->eliminarVentas();
		$this->repartidor_comision_bono->eliminarHistorialComisionBono();
		$this->ingresarBonos();
		$this->ingresarRedDeAfiliacion2();
		$this->ingresarVentas2();
	
		$this->repartidor_comision_bono->eliminarHistorialComisionBono();
		$this->afiliado->eliminarRemanentes();
		$repartidorComisionBono=new$this->repartidor_comision_bono();
	
		$fecha=date('Y-m-d');
		$id_bono=$this->idBonoDeBinario;
	
		$calculadorBono=new $this->calculador_bono();
		$calculadorBono->calcularComisionesPorBono($id_bono,$fecha);
	
		//BONO De Inicio Rapido
	
		$id_usuario=10000;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run("2,162.5",number_format($resultado,1), 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.number_format($resultado,1));
	
		$id_usuario=10001;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run(92.4,number_format($resultado,1), 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado);
	
		$id_usuario=10002;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run(0,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado);
	
		$id_usuario=10003;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run(0,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado);
	
		$id_usuario=10004;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run(0,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado);
	
		$id_usuario=10005;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run(0,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado);
	
		$id_usuario=10006;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run(432.5,number_format($resultado,1), 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado);
	
		$id_usuario=10007;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run(0,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado);
	
		$id_usuario=10008;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run(432.5,number_format($resultado,1), 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado);
	
		$id_usuario=10009;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run(0,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado);
	
		$id_usuario=10010;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run(0,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado);
	
		$id_usuario=10012;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run(0,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado);
	
	
	}
	
	public function testGetTotalDeDineroARepartir(){
		$this->modelo_bono->limpiarTodosLosBonos();
		$this->afiliado->eliminarUsuarios();
		$this->afiliado->eliminarRemanentes();
		$this->red->eliminarRed();
		$this->mercancia->eliminarMercancias();
		$this->mercancia->eliminarCategorias();
		$this->venta->eliminarVentas();
		$this->repartidor_comision_bono->eliminarHistorialComisionBono();
		$this->ingresarBonos();
		$this->ingresarRedDeAfiliacion2();
		$this->ingresarVentas2();
	
		$bono=$this->bono_mobile_money;
	
		$id_red=300;
		
		$fecha=date('Y-m-d');
		$fecha_inicio=$fecha;
		$fecha_fin=$fecha;
		
		$id_usuario=2;
		$resultado=$bono->getAfiliadosPataDebil($id_red,$id_usuario);
		echo $this->unit->run(0,$resultado, 'Test get Total de Afiliados Activos por Afiliacion en la red '.$id_usuario,'Resultado es :'.$resultado);
		
		$id_usuario=10000;
		$resultado=$bono->getAfiliadosPataDebil($id_red,$id_usuario);
		echo $this->unit->run(5,$resultado, 'Test get Total de Afiliados Activos por Afiliacion en la red '.$id_usuario,'Resultado es :'.$resultado);
		
		$id_usuario=10001;
		$resultado=$bono->getAfiliadosPataDebil($id_red,$id_usuario);
		echo $this->unit->run(2,$resultado, 'Test get Total de Afiliados Activos por Afiliacion en la red '.$id_usuario,'Resultado es :'.$resultado);
		
		$id_usuario=10002;
		$resultado=$bono->getAfiliadosPataDebil($id_red,$id_usuario);
		echo $this->unit->run(0,$resultado, 'Test get Total de Afiliados Activos por Afiliacion en la red '.$id_usuario,'Resultado es :'.$resultado);
		
		$id_usuario=10003;
		$resultado=$bono->getAfiliadosPataDebil($id_red,$id_usuario);
		echo $this->unit->run(0,$resultado, 'Test get Total de Afiliados Activos por Afiliacion en la red '.$id_usuario,'Resultado es :'.$resultado);
		
		$id_usuario=10004;
		$resultado=$bono->getAfiliadosPataDebil($id_red,$id_usuario);
		echo $this->unit->run(0,$resultado, 'Test get Total de Afiliados Activos por Afiliacion en la red '.$id_usuario,'Resultado es :'.$resultado);
		
		$id_usuario=10005;
		$resultado=$bono->getAfiliadosPataDebil($id_red,$id_usuario);
		echo $this->unit->run(0,$resultado, 'Test get Total de Afiliados Activos por Afiliacion en la red '.$id_usuario,'Resultado es :'.$resultado);
		
		$id_usuario=10006;
		$resultado=$bono->getAfiliadosPataDebil($id_red,$id_usuario);
		echo $this->unit->run(1,$resultado, 'Test get Total de Afiliados Activos por Afiliacion en la red '.$id_usuario,'Resultado es :'.$resultado);
		
		$id_usuario=10007;
		$resultado=$bono->getAfiliadosPataDebil($id_red,$id_usuario);
		echo $this->unit->run(0,$resultado, 'Test get Total de Afiliados Activos por Afiliacion en la red '.$id_usuario,'Resultado es :'.$resultado);
		
		$id_usuario=10008;
		$resultado=$bono->getAfiliadosPataDebil($id_red,$id_usuario);
		echo $this->unit->run(1,$resultado, 'Test get Total de Afiliados Activos por Afiliacion en la red '.$id_usuario,'Resultado es :'.$resultado);
		
		$id_usuario=10009;
		$resultado=$bono->getAfiliadosPataDebil($id_red,$id_usuario);
		echo $this->unit->run(0,$resultado, 'Test get Total de Afiliados Activos por Afiliacion en la red '.$id_usuario,'Resultado es :'.$resultado);
		
		$id_usuario=10010;
		$resultado=$bono->getAfiliadosPataDebil($id_red,$id_usuario);
		echo $this->unit->run(0,$resultado, 'Test get Total de Afiliados Activos por Afiliacion en la red '.$id_usuario,'Resultado es :'.$resultado);
		
		$id_usuario=10011;
		$resultado=$bono->getAfiliadosPataDebil($id_red,$id_usuario);
		echo $this->unit->run(0,$resultado, 'Test get Total de Afiliados Activos por Afiliacion en la red '.$id_usuario,'Resultado es :'.$resultado);
		
		$id_usuario=10012;
		$resultado=$bono->getAfiliadosPataDebil($id_red,$id_usuario);
		echo $this->unit->run(0,$resultado, 'Test get Total de Afiliados Activos por Afiliacion en la red '.$id_usuario,'Resultado es :'.$resultado);
		
		
		$id_usuario=2;
		$resultado=$bono->getTotalARepartir($id_red,$id_usuario,$fecha_inicio,$fecha_fin);
		echo $this->unit->run(3120,$resultado, 'Test total de monto a repartir en la red','Resultado es :'.$resultado);
	
		$id_usuario=2;
		$resultado=$bono->getTotalIgualaciones($id_red,$id_usuario);
		echo $this->unit->run(9,$resultado, 'Test get Total de igualaciones en la red ','Resultado es :'.$resultado);

		$resultado=$bono->getTotalPorIgualacion($id_red,$fecha_inicio,$fecha_fin);

		echo $this->unit->run(346.7,number_format($resultado,1), 'Test total de monto a repartir para cada afiliado','Resultado es :'.number_format($resultado,1));
		
		$totalRepartirPorIgualacion=$bono->getTotalPorIgualacion($id_red,$fecha_inicio,$fecha_fin);
		$resultado=$bono->getTotalAfiliadosBono3Puntos($id_red,$totalRepartirPorIgualacion,$fecha_inicio,$fecha_fin);

		echo $this->unit->run(600.9,number_format($resultado["valor"],1), 'Test total de afiliados con solo 3 puntos que pueden cobrar','Resultado es :'.number_format($resultado["valor"],1));
		echo $this->unit->run(2,$resultado["numero_igualaciones"], 'Test total de afiliados con solo 3 puntos que pueden cobrar','Resultado es :'.$resultado["numero_igualaciones"]);
		
		$resultado=$bono->getTotalPorIgualacionQueSobra($id_red,$fecha_inicio,$fecha_fin);
		echo $this->unit->run(85.8,number_format($resultado,1), 'Test total de afiliados con solo 3 puntos que pueden cobrar','Resultado es :'.$resultado);

	}

	public function testARecibir(){
		$this->modelo_bono->limpiarTodosLosBonos();
		$this->afiliado->eliminarUsuarios();
		$this->afiliado->eliminarRemanentes();
		$this->red->eliminarRed();
		$this->mercancia->eliminarMercancias();
		$this->mercancia->eliminarCategorias();
		$this->venta->eliminarVentas();
		$this->repartidor_comision_bono->eliminarHistorialComisionBono();
		$this->ingresarBonos();
		$this->ingresarRedDeAfiliacion2();
		$this->ingresarVentas2();
		
		$bono=$this->bono_mobile_money;
		
		$id_red=300;
		
		$fecha=date('Y-m-d');
		$fecha_inicio=$fecha;
		$fecha_fin=$fecha;
		
		$bono->setUpBono($id_red, $fecha_inicio, $fecha_fin);
		
		$id_usuario=10000;
		$resultado=$bono->getTotalARecibirAfiliado($id_red,$id_usuario);
		echo $this->unit->run("2,162.5",number_format($resultado,1), 'Test get Total a recibir por el afiliado '.$id_usuario,'Resultado es :'.number_format($resultado,1));
		
		$id_usuario=10001;
		$resultado=$bono->getTotalARecibirAfiliado($id_red,$id_usuario);
		echo $this->unit->run(92.4,number_format($resultado,1), 'Test get Total a recibir por el afiliado '.$id_usuario,'Resultado es :'.$resultado);
		
		$id_usuario=10006;
		$resultado=$bono->getTotalARecibirAfiliado($id_red,$id_usuario);
		echo $this->unit->run(432.5,number_format($resultado,1), 'Test get Total a recibir por el afiliado '.$id_usuario,'Resultado es :'.$resultado);
		
		$id_usuario=10008;
		$resultado=$bono->getTotalARecibirAfiliado($id_red,$id_usuario);
		echo $this->unit->run(432.5,number_format($resultado,1), 'Test get Total a recibir por el afiliado '.$id_usuario,'Resultado es :'.$resultado);
		
		
	}

	public function testRepartirComisiones(){
		$this->repartidor_comision_bono->eliminarHistorialComisionBono();
		$repartidorComisionBono=new$this->repartidor_comision_bono();
		
		$fecha=date('Y-m-d');
		$id_bono=$this->idBonoDeBinario;
		
		$calculadorBono=new $this->calculador_bono();
		$calculadorBono->calcularComisionesPorBono($id_bono,$fecha);
		
		$id_usuario=10000;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run("2,162.5",number_format($resultado,1), 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado);
		
		$id_usuario=10001;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run(92.4,number_format($resultado,1), 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado);
		
		$id_usuario=10002;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run(0,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado);
		
		$id_usuario=10003;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run(0,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado);
		
		$id_usuario=10004;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run(0,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado);
		
		$id_usuario=10005;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run(0,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado);
		
		$id_usuario=10006;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run(432.5,number_format($resultado,1), 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado);
		
		$id_usuario=10007;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run(0,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado);
		
		$id_usuario=10008;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run(432.5,number_format($resultado,1), 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado);
		
		$id_usuario=10009;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run(0,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado);
		
		$id_usuario=10010;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run(0,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado);
		
		$id_usuario=10011;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run(0,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado);
		
		$id_usuario=10012;
		$resultado=$repartidorComisionBono->getTotalValoresTransaccionPorBonoYUsuario($id_bono,$id_usuario)[0]->total;
		echo $this->unit->run(0,$resultado, 'Test validar si entrega comisiones bono en red al afiliado '.$id_usuario,'Resultado es :'.$resultado);
		
	}
	
	public function testValidarSiElBonoYaCobroVerdadero($id_bono){
		
		$calculadorBono=new $this->calculador_bono();
		$fecha=date('Y-m-d');
		$bono=new $this->bono();$bono->setUpBono($id_bono);
		$resultado=$calculadorBono->isPagado($bono,$fecha);
		echo $this->unit->run(true,$resultado, 'Test validar si el bono ya se pago','Resultado es :'.$resultado);

	}
	
	private function ingresarBonos(){
		$this->modelo_bono->limpiarTodosLosBonos();
		
		/*------------------------------------------------------------------------------- 
		 * [ id_tipo_rango ]= Tipo de condicion que se debe cumplir.
		 * 					  	1.Afiliaciones
		 * 					  	2.Ventas Red
		 * 					  	3.Compras Personales
		 * 					  	4.Puntos Comisionables Personales
		 * 					  	5.Puntos Comisionables Red
		 * 
		 * [condicion1]= (Afiliados) Red (Los demas) Tipo de Mercancia
		 * 												1.Productos
		 * 												2.Servicios
		 * 												3.Combinado
		 * 												4.Paquete de Inscripcion
		 * 												5.Membresia
		 * 
		 * [condicion2]= (Afiliados) Nivel (Los demas) ID Mercancia
		 * 
		 */
		
		
		//----------------------------BONO DE Inicio de Equipo ------------------------------------------------
	

		$rangos=array();

		
		$puntosPersonales=4;
		$cualquiera=0;
		
		$datosRango = array(
				'id_rango' => 57,
				'nombre_rango'   => "Bono de Equipo",
				'descripcion_rango'    => "Bono de Equipo",
				'id_tipo_rango' => $puntosPersonales,
				'valor'   => 1,
				'condicion_red'    => "RED",
				'nivel_red'   => 0,
				'id_condicion' => 1,
				'id_red'   => 300,
				'condicion_red_afilacion'    => "EQU",
				'condicion1'    => $cualquiera,
				'condicion2'	=> $cualquiera,
				'calificado'    => "DOS",
				'estatus_rango'	=> 'ACT'
		);
		
	
		array_push($rangos,$datosRango);
		
		$inicioAfiliacion=0;
		$fechaActual=0;
		
		$datosBono = array(
				'id_bono' => $this->idBonoDeEquipo,
				'nombre_bono'   => "Bono De Equipo",
				'descripcion_bono'    => "Bono De Equipo",
				'plan'	=> "NO",
				'inicio' => '2016-03-01',
				'fin'   => '2026-03-25',
				'frecuencia'    => "MES",
				'mes_desde_afiliacion'	=> $inicioAfiliacion,
				'mes_desde_activacion'	=> $fechaActual,
				'estatus_bono' => "ACT"
		);
		
		
		$datosValoresBono=array();
		
		$datosValoresBono0 = array(
				'id_valor' => 10,
				'id_rango'   => $this->idBonoDeEquipo,
				'nivel'    => 0,
				'condicion_red'    => "RED",
				'verticalidad'    => "PASC",
				'valor'	=> 0
		);
		
		$datosValoresBono1 = array(
				'id_valor' => 11,
				'id_rango'   => $this->idBonoDeEquipo,
				'nivel'    => 1,
				'condicion_red'    => "RED",
				'verticalidad'    => "PASC",
				'valor'	=> 0
		);
		
		$datosValoresBono2 = array(
				'id_valor' => 12,
				'id_rango'   => $this->idBonoDeEquipo,
				'nivel'    => 2,
				'condicion_red'    => "RED",
				'verticalidad'    => "PASC",
				'valor'	=> 2
		);
		
		$datosValoresBono3 = array(
				'id_valor' => 13,
				'id_rango'   => $this->idBonoDeEquipo,
				'nivel'    => 3,
				'condicion_red'    => "RED",
				'verticalidad'    => "PASC",
				'valor'	=> 5
		);
	
		$datosValoresBono4 = array(
				'id_valor' => 14,
				'id_rango'   => $this->idBonoDeEquipo,
				'nivel'    => 4,
				'condicion_red'    => "RED",
				'verticalidad'    => "PASC",
				'valor'	=> 4
		);
		
		$datosValoresBono5 = array(
				'id_valor' => 15,
				'id_rango'   => $this->idBonoDeEquipo,
				'nivel'    => 5,
				'condicion_red'    => "RED",
				'verticalidad'    => "PASC",
				'valor'	=> 2
		);
		
		$datosValoresBono6 = array(
				'id_valor' => 16,
				'id_rango'   => $this->idBonoDeEquipo,
				'nivel'    => 6,
				'condicion_red'    => "RED",
				'verticalidad'    => "PASC",
				'valor'	=> 1
		);
		
		$datosValoresBono7 = array(
				'id_valor' => 17,
				'id_rango'   => $this->idBonoDeEquipo,
				'nivel'    => 7,
				'condicion_red'    => "RED",
				'verticalidad'    => "PASC",
				'valor'	=> 1
		);
		
		$datosValoresBono8 = array(
				'id_valor' => 18,
				'id_rango'   => $this->idBonoDeEquipo,
				'nivel'    => 8,
				'condicion_red'    => "RED",
				'verticalidad'    => "PASC",
				'valor'	=> 0.5
		);
		
		$datosValoresBono9 = array(
				'id_valor' => 19,
				'id_rango'   => $this->idBonoDeEquipo,
				'nivel'    => 9,
				'condicion_red'    => "RED",
				'verticalidad'    => "PASC",
				'valor'	=> 0.5
		);
		
		$datosValoresBono10 = array(
				'id_valor' => 20,
				'id_rango'   => $this->idBonoDeEquipo,
				'nivel'    => 10,
				'condicion_red'    => "RED",
				'verticalidad'    => "PASC",
				'valor'	=> 0.5
		);
		
		array_push($datosValoresBono, $datosValoresBono0,$datosValoresBono1,$datosValoresBono2,$datosValoresBono3,$datosValoresBono4,
				   $datosValoresBono5,$datosValoresBono6,$datosValoresBono7,$datosValoresBono8,$datosValoresBono9,$datosValoresBono10);
		$nuevoBono=new $this->modelo_bono();
		$nuevoBono->nuevoBonoVariosRangos ($rangos,$datosBono,$datosValoresBono);

		//----------------------------BONO Binario ------------------------------------------------
/*		
		
		$rangos=array();
		
		
		$afiliados=1;
		$cualquiera=0;
		
		$datosRango = array(
				'id_rango' => 58,
				'nombre_rango'   => "Binario",
				'descripcion_rango'    => "Binario",
				'id_tipo_rango' => $afiliados,
				'valor'   => 0,
				'condicion_red'    => "RED",
				'nivel_red'   => 0,
				'id_condicion' => 2,
				'id_red'   => 300,
				'condicion_red_afilacion'    => "DEB",
				'condicion1'    => $cualquiera,
				'condicion2'	=> $cualquiera,
				'calificado'    => "DOS",
				'estatus_rango'	=> 'ACT'
		);
		
		
		array_push($rangos,$datosRango);
		
		$inicioAfiliacion=0;
		$fechaActual=0;
		
		$datosBono = array(
				'id_bono' => $this->idBonoDeBinario,
				'nombre_bono'   => "Bono Binario",
				'descripcion_bono'    => "Bono Binario",
				'plan'	=> "NO",
				'inicio' => '2016-03-01',
				'fin'   => '2026-03-25',
				'frecuencia'    => "MES",
				'mes_desde_afiliacion'	=> $inicioAfiliacion,
				'mes_desde_activacion'	=> $fechaActual,
				'estatus_bono' => "ACT"
		);
		
		
		$datosValoresBono=array();
		
		$datosValoresBonoAfiliado = array(
				'id_valor' => 10,
				'id_rango'   => $this->idBonoDeBinario,
				'nivel'    => 0,
				'condicion_red'    => "DIRECTOS",
				'verticalidad'    => "RDESC",
				'valor'	=> 0
		);
		
		array_push($datosValoresBono, $datosValoresBonoAfiliado);
		$nuevoBono=new $this->modelo_bono();
		$nuevoBono->nuevoBonoVariosRangos ($rangos,$datosBono,$datosValoresBono);
		*/
	}

	private function ingresarRedDeAfiliacion(){
		
		$id_red=300;
		
		$red=$this->red;
		$datosRed = array(
				'id_red' => $id_red,
				'nombre'   => "WoW CONEXION",
				'descripcion'    => "Construye un equipo, para darle alas a tus emprendimientos.",
				'frontal' => 6,
				'profundidad'   => 10,
				'valor_punto'    => 1000,
				'estatus'   => 'ACT',
				'plan' => 'BIN'
		);
		
		$red->nuevaRed($datosRed);
		$red->ingresarRed();

		$this->ingresarAfiliado($id_red,10000,"giovanny",2,2,0);
		$this->ingresarAfiliado($id_red,10001,"carlos",10000,10000,0);
		$this->ingresarAfiliado($id_red,10002,"pedro",10000,10000,1);
		$this->ingresarAfiliado($id_red,10003,"camilo",10000,10000,2);
		$this->ingresarAfiliado($id_red,10004,"Nicolas",10000,10000,3);
		$this->ingresarAfiliado($id_red,10005,"esperanza",10000,10000,4);
		$this->ingresarAfiliado($id_red,10006,"maria",10000,10000,5);
		$this->ingresarAfiliado($id_red,10007,"pepe",10001,10001,0);
		$this->ingresarAfiliado($id_red,10008,"dario",10003,10003,1);
		$this->ingresarAfiliado($id_red,10009,"diego",10006,10006,0);
		$this->ingresarAfiliado($id_red,10010,"andres",10006,10006,1);
		$this->ingresarAfiliado($id_red,10011,"ricardo",10007,10007,0);
		$this->ingresarAfiliado($id_red,10012,"miguel",10007,10007,1);
		$this->ingresarAfiliado($id_red,10013,"paola",10009,10009,0);
		$this->ingresarAfiliado($id_red,10014,"fernando",10009,10009,1);
		$this->ingresarAfiliado($id_red,10015,"laura",10012,10012,0);
		$this->ingresarAfiliado($id_red,10016,"david",10012,10012,1);
		$this->ingresarAfiliado($id_red,10017,"mario",10014,10014,0);
		$this->ingresarAfiliado($id_red,10018,"andrea",10014,10014,1);
		$this->ingresarAfiliado($id_red,10019,"joan",10016,10016,0);
		$this->ingresarAfiliado($id_red,10020,"alejandro",10016,10016,1);
		$this->ingresarAfiliado($id_red,10021,"marcel",10017,10017,0);
		$this->ingresarAfiliado($id_red,10022,"daniel",10017,10017,1);
		$this->ingresarAfiliado($id_red,10023,"julian",10019,10019,0);
		$this->ingresarAfiliado($id_red,10024,"german",10019,10019,1);
		$this->ingresarAfiliado($id_red,10025,"luis",10020,10020,0);
		$this->ingresarAfiliado($id_red,10026,"alberto",10020,10020,1);
		$this->ingresarAfiliado($id_red,10027,"carolina",10022,10022,0);
		$this->ingresarAfiliado($id_red,10028,"haroll",10022,10022,1);
		$this->ingresarAfiliado($id_red,10029,"ruben",10023,10023,0);
		$this->ingresarAfiliado($id_red,10030,"marcela",10024,10024,0);
		$this->ingresarAfiliado($id_red,10031,"nelly",10026,10026,0);
		$this->ingresarAfiliado($id_red,10032,"jose",10030,10030,0);
		$this->ingresarAfiliado($id_red,10033,"johana",10030,10030,1);
		$this->ingresarAfiliado($id_red,10034,"pablo",10032,10032,0);
		$this->ingresarAfiliado($id_red,10035,"daniel",10032,10032,1);
	}

	private function ingresarAfiliado($id_red,$id,$nombre,$debajo_de,$sponsor,$lado){
		$afiliador=new $this->modelo_bono();
		$afiliador->crearNuevoUsuario ($id,$nombre,"2016-03-17",$id,$id_red,$debajo_de,$sponsor,$lado);
	}
	
	private function ingresarMercancia($id_mercancia,$nombre,$id_categoria,$id_tipo_mercancia,$costo,$puntos_comisionables){
		$datos = array(
				'id' => $id_mercancia,
				'sku' => $id_mercancia,
				'sku_2' => $id_mercancia,
				'id_tipo_mercancia'   => $id_tipo_mercancia,
				'pais' => "AAA",
				'estatus' => "ACT",
				'id_proveedor' => "0",
				'real'    => $costo,
				'costo'    => $costo,
				'costo_publico'    => $costo,
				'entrega'    =>0,
				'iva'    =>"MAS",
				'descuento'    =>"0",
				'puntos_comisionables'=>$puntos_comisionables
		
		);
		$this->db->insert('mercancia',$datos);
		
		$datos = array(
				'id_mercancia' => $id_mercancia,
				'id_cat_imagen' => "10000",
		
		);
		
		$this->db->insert('cross_merc_img',$datos);
		
		if($id_tipo_mercancia==1){
			$datos = array(
					'id' => $id_mercancia,
					'nombre'=>$nombre,
					'id_grupo'   => $id_categoria
						
			);
			$this->db->insert('producto',$datos);
				
		}else if($id_tipo_mercancia==2){
			$datos = array(
					'id' => $id_mercancia,
					'nombre'=>$nombre,
					'id_red'   => $id_categoria
						
			);
			$this->db->insert('servicio',$datos);
		}else if($id_tipo_mercancia==3){
			$datos = array(
					'id' => $id_mercancia,
					'nombre'=>$nombre,
					'id_red'   => $id_categoria
						
			);
			$this->db->insert('combinado',$datos);
		}else if($id_tipo_mercancia==4){
			$datos = array(
					'id_paquete' => $id_mercancia,
					'nombre'=>$nombre,
					'id_red'   => $id_categoria
						
			);
			$this->db->insert('paquete_inscripcion',$datos);
		}else if($id_tipo_mercancia==5){
			$datos = array(
					'id' => $id_mercancia,
					'nombre'=>$nombre,
					'id_red'   => $id_categoria
						
			);
			$this->db->insert('membresia',$datos);
		}
	}

	private function ingresarVentaMercanciaUsuario($id_venta,$id_usuario,$fecha,$mercanciasVendidas){
		
		$datosMercanciasVendidas=array();
		
		foreach ($mercanciasVendidas as $mercanciaId){
			$mercancia=new $this->mercancia ();
			$mercancia->setUpMercancia($mercanciaId);
			
			$datosMercancia = array(
					'id_mercancia' => $mercancia->getIdMercancia(),
					'costo_total'   => $mercancia->getCosto(),
					'puntos_comisionables'    => $mercancia->getPuntosComisionables(),
			);
			
			array_push($datosMercanciasVendidas,$datosMercancia);
		}

		
		$datosVenta = array(
				'id_venta' =>$id_venta,
				'id_usuario'   => $id_usuario,
				'estatus'    => 'ACT',
				'fecha' => $fecha,
				'mercancia'=>$datosMercanciasVendidas
		);
		
		$this->venta->nuevaVenta ($datosVenta);
		$this->venta->ingresarVenta ();
	}

	private function ingresarVentasFecha($fecha,$categoria,$ids){

		if($categoria){
				
				$id_categoria=250;
				
				$datosCategoria = array(
						'id_categoria' => 250,
						'id_red'   => 300,
				);
				
				$this->mercancia->ingresarCategoria ($datosCategoria);
				
				/*  TIPO DE MERCANCIA
				 *  Producto  = 1
				 *  Servicios = 2
				 * 	Combinado = 3
				 *  Paquete.I = 4
				 * 	Membresia = 5
				 *
				*/
				
				$producto=1;
				$servicio=2;
				$membresia=5;
				
				
				$id=1;$costo=68000;$puntos=0;
				$this->ingresarMercancia($id,"Kit de Afiliación(Winner)",$id_categoria,$membresia,$costo,$puntos);
				
				$id=4;$costo=80000;$puntos=80;
				$this->ingresarMercancia($id,"Plan Educativo Anual",$id_categoria,$membresia,$costo,$puntos);
				
				$id=7;$costo=130000;$puntos=120;
				$this->ingresarMercancia($id,"Paquete Mensual (Telefonía + Plan Educativo) Winner",$id_categoria,$producto,$costo,$puntos);
				
				$id=6;$costo=30000;$puntos=0;
				$this->ingresarMercancia($id,"Kit de Afiliación (Basic)",$id_categoria,$producto,$costo,$puntos);
				
				$id=8;$costo=80000;$puntos=35;
				$this->ingresarMercancia($id,"Paquete Mensual (Telefonía + Plan Educativo) Basic",$id_categoria,$servicio,$costo,$puntos);
		
				$id=505;$costo=60000;$puntos=3;
				$this->ingresarMercancia($id,"Recarga Telefonía 60000",$id_categoria,$producto,$costo,$puntos);
				
				$id=506;$costo=45000;$puntos=34;
				$this->ingresarMercancia($id,"Plan Educativo Basic",$id_categoria,$servicio,$costo,$puntos);
				
		
		
		}
	
/*							RED DE AFILIACION
*           	            			 ____________
*           	            			| GIOVANNY   |
*           							| ID:10000   | 
*        	   	           				|  Spr:_2    |
*        			  					|Merc:  W    |
*               	   					|Total:278000|
*        			  					|Puntos:200  |
*        _______/__    ___/______  __\________  _____\____    ___\______     ___\_____
*       |  CARLOS  | |   PEDRO  | |   CAMILO | | NICOLAS  |  | ESPERANZA|   |   MARIA  | 
*       | ID:10001 | | ID:10002 | | ID:10003 | | ID:10004 |  | ID:10005 |   | ID:10006 |
*       | Spr:10000| |Spr:10000 | |_Spr:10000| |_Spr:10000|  |_Spr:10000|   |_Spr:10000|
*       |Merc: B   | |Merc: B   | |Merc:     | |Merc:  W  |  |Merc:     |   |Merc:  B  |
*       |To:110000 | |To:110000	| |To:60000  | |To:278000 |  |To:45000  |   |To:110000 |
*       |Puntos: 35| |Puntos: 35| |Puntos: 3 | |Puntos:200|  |Puntos:34 |   |Puntos: 35|      
*		    ___/_____              __|_______                           _____/____     __\_______
*         | PEPE     |             | DARIO    |                        |  DIEGO   |   |  ANDRES  |
*         | ID:10007 |             | ID:10008 |                        | ID:10009 |   | ID:10010 |
*         |_Spr:10003|             |_Spr:10001|                        |_Spr:10000|   |_Spr:10000|
*         |Merc:  B  |             |Merc:  B  |                        |Merc : B  |   |Merc : W  |
*         |To:110000 |             |To:110000 |                        |To:110000 |   |To:278000 |
*         |Puntos: 35|             |Puntos: 35|                        |Puntos: 35|   |Puntos:200|
*  _______/__   _____\____                                       _____/____    __\_______
* | RICARDO  | | MIGUEL   |                                     |  PAOLA   |   | FERNANDO |
* | ID:10011 | | ID:10012 |                                     | ID:10013 |   | ID:10014 |
* |_Spr:10007| |_Spr:10007|                                     |_Spr:10010|   |_Spr:10010|
* |Merc:     | |Merc: W   |                                     |Merc:  B  |   |Merc:     |
* |To:60000  | |To:278000 |                                     |To:110000 |   |To: 60000 |
* |Puntos: 3 | |Puntos:200|                                     |Puntos: 35|   |Puntos: 3 |
*        _______/__   _____\____                                            ____/____     _\_________
*       | LAURA    | | DAVID    |                                          |  MARIO   |   | ANDREA   |
*       | ID:10015 | | ID:10016 |                                          | ID:10017 |   | ID:10018 |
*       |_Spr:10012| |_Spr:10012|                                          |_Spr:10014|   |_Spr:10014|
*       |Merc:  W  | |Merc:  W  |                                          |Merc:  B  |   |Merc: B   |
*       |To:278000 | |To:278000 |                                          |Tot:110000|   |Tot:110000|
*       |Puntos:200| |Puntos:200|                                          |Puntos: 35|   |Puntos: 35|
*              _______/___  _____\____                                 ____/____      _\________
*             | JOAN     | |ALEJANDRO |                               | MARCEL   |   | DANIEL   |
*             | ID:10019 | | ID:10020 |                               | ID:10021 |   | ID:10022 |
*             |_Spr:10016| |_Spr:10001|                               |_Spr:10017|   |_Spr:10017|
*             |Merc:     | |Merc: B   |                               |Merc:  W  |   |Merc:  W  |
*             |To:30000  | |To:110000 |                               |To:278000 |   |To: 278000|
*             |Puntos: 0 | |Puntos: 35|\                              |Puntos:200|   |Puntos:200|
* __________/  _____\____   _/________  \__________                          ___________/ ___\______
*| JULIAN   | | GERMAN   | |  LUIS    | |ALBERTO   |                        | CAROLINA | | HAROLL   |
*| ID:10023 | | ID:10024 | | ID:10025 | | ID:10026 |                        | ID:10027 | | ID:10028 |
*|_Spr:10019| |_Spr:10019| |_Spr:10016| |_Spr:10020|                        |_Spr:10022| |_Spr:10022|
*|Merc:  B  | |Merc:  W  | |Merc:  W  | |Merc:     |                        |Merc:  B  | |Merc: W   |
*|To:110000 | |to:278000 | |To:278000 | |To: 68000 |                        |To: 110000| |To:278000 |
*|Puntos: 35| |Puntos:200| |Puntos:200| |Puntos: 0 |                        |Puntos:35 | |Puntos:200|
*_____|_____     ____|_____               ____|_____            
*|  RUBEN   |   |   MARCELA|             |  NELLY   |    	     
*| ID:10029 |   | ID:10030 |       	     | ID:10031 |            
*|Spr:10001 |   |Spr:10001 |             |Spr:10026 |            
*|Merc:  B  |   |Merc:     |             |Merc:  B  |            
*|To: 110000|   |To: 148000|             |To: 110000|            
*|Puntos: 35|   |Puntos:80 |             |Puntos: 35|            
*         __________/   ____\____
*        |  JOSE    | | JOHANA   |
*        | ID:10032 | | ID:10033 |
*		 |_Spr:10030| |_Spr:10030|
*        |Merc:  B  | |Merc:     | 
*        |To: 148000| |To: 68000 |
*        |Puntos:80 | |Puntos: 0 |
      _____/_____  \__________
*     |  PABLO   | | DANIEL   |
*     | ID:10034 | | ID:10035 |
*     |_Spr:10032| |_Spr:10032|
*     |Merc:  B  | |Merc: W   |
*     |To:110000 | |To: 278000|
*     |Puntos: 35| |Puntos:200|   
*     
*     
*/
	
		$this->ingresarVentaMercanciaUsuario($ids,10000,$fecha,array(1,7,4));//Winner
		$this->ingresarVentaMercanciaUsuario($ids+1,10001,$fecha,array(6,8));//Basic
		$this->ingresarVentaMercanciaUsuario($ids+2,10002,$fecha,array(6,8));//Basic
		$this->ingresarVentaMercanciaUsuario($ids+3,10003,$fecha,array(505));
		$this->ingresarVentaMercanciaUsuario($ids+4,10004,$fecha,array(1,7,4));//Winner
		$this->ingresarVentaMercanciaUsuario($ids+5,10005,$fecha,array(506));
		$this->ingresarVentaMercanciaUsuario($ids+6,10006,$fecha,array(6,8));//Basic
		$this->ingresarVentaMercanciaUsuario($ids+7,10007,$fecha,array(6,8));//Basic
		$this->ingresarVentaMercanciaUsuario($ids+8,10008,$fecha,array(6,8));//Basic
		$this->ingresarVentaMercanciaUsuario($ids+9,10009,$fecha,array(6,8));//Basic
		$this->ingresarVentaMercanciaUsuario($ids+10,10010,$fecha,array(1,7,4));//Winner
		$this->ingresarVentaMercanciaUsuario($ids+11,10011,$fecha,array(505));
		$this->ingresarVentaMercanciaUsuario($ids+12,10012,$fecha,array(1,7,4));//Winner
		$this->ingresarVentaMercanciaUsuario($ids+13,10013,$fecha,array(6,8));//Basic
		$this->ingresarVentaMercanciaUsuario($ids+14,10014,$fecha,array(505));
		$this->ingresarVentaMercanciaUsuario($ids+15,10015,$fecha,array(1,7,4));//Winner
		$this->ingresarVentaMercanciaUsuario($ids+16,10016,$fecha,array(1,7,4));//Winner
		$this->ingresarVentaMercanciaUsuario($ids+17,10017,$fecha,array(6,8));//Basic
		$this->ingresarVentaMercanciaUsuario($ids+18,10018,$fecha,array(6,8));//Basic
		$this->ingresarVentaMercanciaUsuario($ids+20,10020,$fecha,array(6,8));//Basic
		$this->ingresarVentaMercanciaUsuario($ids+21,10021,$fecha,array(1,7,4));//Winner
		$this->ingresarVentaMercanciaUsuario($ids+22,10022,$fecha,array(1,7,4));//Winner
		$this->ingresarVentaMercanciaUsuario($ids+23,10023,$fecha,array(6,8));//Basic
		$this->ingresarVentaMercanciaUsuario($ids+24,10024,$fecha,array(1,7,4));//Winner
		$this->ingresarVentaMercanciaUsuario($ids+25,10025,$fecha,array(1,7,4));//Winner
		$this->ingresarVentaMercanciaUsuario($ids+26,10026,$fecha,array(1));
		$this->ingresarVentaMercanciaUsuario($ids+27,10027,$fecha,array(6,8));//Basic
		$this->ingresarVentaMercanciaUsuario($ids+28,10028,$fecha,array(1,7,4));//Winner
		$this->ingresarVentaMercanciaUsuario($ids+29,10029,$fecha,array(6,8));//Basic
		$this->ingresarVentaMercanciaUsuario($ids+30,10030,$fecha,array(4));
		$this->ingresarVentaMercanciaUsuario($ids+31,10031,$fecha,array(6,8));//Basic
		$this->ingresarVentaMercanciaUsuario($ids+32,10032,$fecha,array(4));//Basic
		$this->ingresarVentaMercanciaUsuario($ids+33,10033,$fecha,array(6));
		$this->ingresarVentaMercanciaUsuario($ids+34,10034,$fecha,array(6,8));//Basic
		$this->ingresarVentaMercanciaUsuario($ids+35,10035,$fecha,array(1,7,4));//Winner

	}
}