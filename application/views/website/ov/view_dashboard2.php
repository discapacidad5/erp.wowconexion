<?php $ci = &get_instance();
   	$ci ->load ->model("model_permissions");?>
			<!-- MAIN CONTENT -->
			<div id="content" >

				<!-- row -->
				<div class="row">
				<br /><br /><br />
				</div>
				<!-- end row -->
     			 <div class="row">
					<div class="col-sm-12">
							<div class="well well-sm">
								<div class="row">
					      	<div class="col-sm-12 col-md-12 col-lg-6">
										<div class="well well-light well-sm no-margin no-padding">
											<div class="row">
												<div class="col-sm-12">
													<div id="myCarousel" class="carousel fade profile-carousel">
														<div class="air air-top-left padding-10">
															<h4 class="txt-color-white font-md"></h4>
														</div>
														<div class="carousel-inner">
															<!-- Slide 1 -->
															<div class="item active">
																<img src="/media/imagenes/m3.jpg" alt="demo user">
															</div>
														</div>
													</div>
												</div>

												<div class="col-sm-12">

													<div class="row">

														<div class="col-sm-3 profile-pic">
															<img src="<?=$user?>" alt="demo user">
															<div class="padding-10">
															<!--	<h4 class="font-md"><strong>1,543</strong>
																<br>
																<small>Followers</small></h4>
																<br>
																<h4 class="font-md"><strong>419</strong>
																<br>
																<small>Connections</small></h4> -->
															</div>
														</div>
														<div class="col-sm-6">
															<h1><?=$usuario[0]->nombre?> <span class="semi-bold"><?=$usuario[0]->apellido?></span>
															<br>
															<small> <?php //echo $nivel_actual_red?></small></h1>

															<ul class="list-unstyled ">
                                <li>
                                <div class="demo-icon-font">
																		<img class="flag flag-<?=strtolower($pais)?>">
                                </div>
																</li>
																<li>
																	<p class="text-muted">
																		<i class="fa fa-phone"></i>&nbsp;&nbsp;(<span class="txt-color-darken"><?=$telefono?></span>)</span>
																	</p>
																</li>
																<li>
																	<p class="text-muted">
																		<i class="fa fa-envelope"></i>&nbsp;&nbsp;<a ><?=$email?></a>
																	</p>
																</li>
																<li>
																	<p class="text-muted">
																		<i class="fa fa-user"></i>&nbsp;&nbsp;<span class="txt-color-darken"><?=$username?></span>
																	</p>
																</li>
																<li>
																	<p class="text-muted">
																		<i class="fa fa-calendar"></i>&nbsp;&nbsp;<span class="txt-color-darken">Ultima sesión: <a href="javascript:void(0);" rel="tooltip" title="" data-placement="top" data-original-title="Create an Appointment"><?=$ultima?></a></span>
																	</p>
																</li>
                                <li>
                                <?php if($id_sponsor&&$name_sponsor){
                                if(($id_sponsor[0]->id_usuario!=1)){
                                ?>
                               <b>Patrocinador:</b>
                              <?=$name_sponsor[0]->nombre?> <?=$name_sponsor[0]->apellido?> con id <?=$id_sponsor[0]->id_usuario?><br/>

                              <?php }else{?>
                              Eres un nodo raíz, fuiste patrocinado por la empresa<br />
                              <?php }}?>
                                </li>
															</ul>
															<br>
															<strong class="<?php echo "label label-success";?>" style="font-size: 2rem;"> Comsumidor</strong>
														</div>
															
														<div class="col-sm-3">
														</div>
													</div>
												</div>
											</div>
  									</div>
									</div>
                					<div>
					<div class="row">
						
							   <div class="col-sm-12 col-md-12 col-lg-4">
									<!--Inica la secciion de la perfil y red-->
									<div class="well" style=""> <!-- box-shadow: 0px 0px 0px !important;border-color: transparent; -->
										<fieldset>
											<legend><b>Perfil y red</b></legend>
											<div class="row">
												<div class="col-sm-6">
													<a href="/ov/perfil_red/perfil">
														<div class="well well-sm txt-color-white text-center link_dashboard" style="background:<?=$style[0]->btn_1_color?>">
															<i class="fa fa-user fa-3x"></i>
															<h5>Perfil</h5>
														</div>
													</a>
												</div>
												<div class="col-sm-6">
													<a href="/ov/compras/carrito">
														<div class="well well-sm txt-color-white text-center link_dashboard" style="background:<?=$style[0]->btn_2_color?>">
															<i class="fa fa-shopping-cart fa-3x"></i>
															<h5>Carrito</h5>
														</div>
													</a>
												</div>
											</div>
											<div class="row">
												<div class="col-sm-6">
													<a href="compras/reportes"> <!-- /ov/perfil_red/afiliar?tipo=1 -->
														<div class="well well-sm txt-color-white text-center link_dashboard" style="background:<?=$style[0]->btn_2_color?>">
															<i class="fa fa-table fa-3x"></i> <!-- fa-edit -->
															<h5>Reportes</h5> <!-- Afiliar -->
														</div>
													</a>
												</div>
												<div class="col-sm-6">
													<a href="/auth/logout">
														<div class="well well-sm txt-color-white text-center link_dashboard" style="background:<?=$style[0]->btn_1_color?>">
															<i class="fa fa-external-link fa-3x"></i>
															<h5>Salir</h5>
														</div>
													</a>
												</div>
											</div>
										</div>
									</fieldset>
									<!--Termina la secciion de perfil y red-->
								</div>
							</div>
							
						</div>

					
						    </div>
				      </div>
          </div>
        </div>
				<!-- end row -->
				
				<div class="row">
	        <!-- a blank row to get started -->
	        <div class="col-sm-12">
	            <br />
	            <br />
	        </div>
        </div>
			</div>
			<!-- END MAIN CONTENT -->
