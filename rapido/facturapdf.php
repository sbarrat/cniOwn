<?php
// OBSOLETA BORRAR
require_once '../inc/configuracion.php';
require_once 'funcionesRapido.php';
require_once 'tcpdf/config/lang/spa.php';
require_once 'tcpdf/tcpdf.php';

if ( isset( $_REQUEST['factura'] ) ) {
	sanitize($_REQUEST);
	$factura = $_REQUEST['factura'];
	// create new PDF document
	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('Perfect Place');
	$pdf->SetTitle('Factura ' . $factura );
	
	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	//set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	//set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	//set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	//set some language-dependent strings
	$pdf->setLanguageArray($l);
	// set font
	$pdf->SetFont('dejavusans', '', 10);
	// add a page
	$pdf->AddPage();
	
	// Cabezera de la factura
	$datosCabezera = cabezeraFactura( $factura );
	if ( isset( $_REQUEST['dup'] ) ) {
		$tipoFactura = "<strong>FACTURA (DUPLICADO)</strong>";	
	} else {
		$tipoFactura = "<strong>FACTURA</strong>";
	}
	// Vamos a poner los datos en variables para que quede mas claro
	$cliente = $datosCabezera[6];
	$fecha = cambiaf( $datosCabezera[5], true );
	$html = <<< EOD
	<table width='100%'>
		<tr>
		<td width='75%'>
			<img src="logo_n.jpg" />
		</td>
		<td width='25%'>
		<h2>$tipoFactura</h2><br/>
		$cliente<br/>
		Fecha: $fecha<br/>
		Num Factura: $factura<br/>
		$datosCabezera[0]<br/>
		$datosCabezera[1]<br/>
		$datosCabezera[2] $datosCabezera[3]<br/>
		$datosCabezera[4]
		</td>
		</tr>
	</table>
	
	
EOD;
	// output the HTML content
	$pdf->writeHTML($html, true, false, true, false, '');
	// reset pointer to the last page
	$pdf->lastPage();
	//Close and output PDF document
	$pdf->Output('factura' . $factura. '.pdf', 'I');	
}	
		/*$pdf =& new Cezpdf('a4');
		$all = $pdf->openObject();
		$pdf->saveState();
		$euro_diff = array(33=>'Euro'); 
		$pdf->selectFont('fonts/Helvetica.afm', 
        array('encoding'=>'WinAnsiEncoding','differences'=>$euro_diff));
		$pdf->addInfo('Title','Factura');
		$pdf->addInfo('Author','Independencia Centro de Negocios');
		$pdf->ezSetCmMargins(3,2,1.2,1.2);
		$im = imagecreatefromjpeg("logo_n.jpg");
		$pdf->addImage($im,33,740,200);
        //10 aniversario
        $gif = imagecreatefromgif("image001.gif");
        $pdf->addImage(&$gif, 470, 750, 90);
		//fin 10 aniversario
        $im = imagecreatefromjpeg("pie_n.jpg");
		$pdf->addImage($im,0,0,600);
		$im = imagecreatefromjpeg("nif_n.jpg");
		$pdf->addImage($im,0,200,33);
		$pdf->restoreState();
		$pdf->closeObject();
		$pdf->addObject( $all, 'all' );*/

//Cabezera de la factura
/*		$sql ="Select c.Nombre,c.Direccion,c.CP,c.Ciudad,
		c.NIF,r.fecha, r.pedidoCliente, c.id from clientes as c 
		join regfacturas as r on r.id_cliente = c.id 
		where r.codigo like $factura";
		$consulta = mysql_query( $sql, $con );
		$resultado = mysql_fetch_array($consulta);
		if((isset($_GET[dup]))||(isset($_POST[dup])))
			$pdf->addText(363,730,16,"<b>FACTURA (DUPLICADO)<b>");
		else
			$pdf->addText(463,730,16,"<b>FACTURA<b>");
		$pdf->rectangle(263,660,280,50);
//ID CLIENTE
		$cliente = $resultado[6];
		$texto="FECHA:".cambiaf($resultado[5]);
		$pdf->addText(50,700,12,$texto);
		$texto="Num. FACTURA:".$factura;
		$pdf->addText(50,685,12,$texto);
//Datos cliente
		$pdf->addText(265,698,10,"<b>".$resultado[0]."</b>");
		$pdf->addText(265,687,10,"<b>".$resultado[1]."</b>");
		$pdf->addText(265,676,10,"<b>".$resultado[2]."-".$resultado[3]."</b>");
		$pdf->addText(265,665,10,"<b>NIF:".$resultado[4]."</b>");
//Asi se pone el fondo en todas
     */
//Paso de datos de historico
/*	$sql = "Select * from historico where factura like '$factura'";
	$consulta = mysql_query( $sql, $con );
	for($i=0;$i<=3;$i++)
		while($resultado=mysql_fetch_array($consulta))
		{
			$importe_sin_iva = $resultado[cantidad]*$resultado[unitario];
			$data[]=array("Servicio"=>ucfirst($resultado[2])." ".ucfirst($resultado[6]),
			"Cant."=>number_format($resultado[cantidad],2,',','.'),
			"P/Unitario"=>number_format($resultado[unitario],2,',','.')."!",
			"Importe"=>number_format($importe_sin_iva,2,',','.')."!",
			"IVA"=>$resultado[iva]."%",
			"TOTAL"=>number_format(iva($importe_sin_iva,$resultado[iva]),2,',','.')."!");
			$total = $total + iva($importe_sin_iva,$resultado[5]);
			$bruto = $bruto + $importe_sin_iva;
			$celdas++;
			//$cantidad++;
			$cantidad = $cantidad + number_format($resultado[cantidad],2,',','.');
			$j++;
		}
		for($k=$j;$k<=30;$k++)
			$data[]=array("Servicio"=>"",
			"Cant."=>"",
			"P/Unitario"=>"",
			"Importe"=>"",
			"IVA"=>"",
			"TOTAL"=>"");
		/*Nueva consulta para devolver los totales bien*/
/*		$sql = "select sum(cantidad), sum(unitario*cantidad) as unitario ,
sum(round((cantidad*unitario)*(iva/100),2)) as iva,
sum((cantidad*unitario) + round((cantidad*unitario)*(iva/100),2)) as total
from historico where factura like '$factura' group by factura";	
	$consulta = @mysql_query( $sql, $con );
	$resultado = @mysql_fetch_array($consulta);
		$data[]=array("Servicio"=>'TOTALES',
"Cant."=>number_format($resultado[0],2,',','.'),"P/Unitario"=>"","Importe"=>number_format($resultado[1],2,',','.')."!","IVA"=>"","TOTAL"=>number_format($resultado[3],2,',','.')."!");

		$pdf->ezSetY(640);
//Opciones de tabla
		$options = array("width"=>500,"maxWidth"=>500,"shadeCol"=>array(0.866,0.866,0.866),
		"cols"=>array(
			'Cant.'=>array('justification'=>'right'),
			'P/Unitario'=>array('justification'=>'right'),
			'Importe'=>array('justification'=>'right'),
			'IVA'=>array('justification'=>'center'),
			'TOTAL'=>array('justification'=>'right')));

		$pdf->ezTable($data,6,"",$options);
		$pie[]=array("TOTAL BRUTO"=>number_format($resultado[1],2,',','.')."!","IVA"=>number_format($resultado[2],2,',','.')."!","TOTAL"=>number_format($resultado[3],2,',','.')."!");
		$pdf->ezText("");
		$pdf->ezTable($pie,3,"",
			array('xPos'=>'398','width'=>'300','maxWidth'=>'300',
				'cols'=>array('TOTAL BRUTO'=>array('justification'=>'center'),
				'IVA'=>array('justification'=>'center'),
				'TOTAL'=>array('justification'=>'center'))));

		/*Modificar para sacar de regfacturas*/
/*		$sql = "Select fpago,obs_fpago,obs_alt, pedidoCliente from regfacturas where codigo like $factura";
		$consulta = mysql_query( $sql, $con );
		$resultado = mysql_fetch_array($consulta);
//$pdf->ezText("");
		$pdf->ezSetY( 115 );
		$pdf->ezText("Forma de Pago:" .$resultado[0]);
		//if(($resultado[fpago] != "Cheque") && ($resultado[fpago] != "Contado") && ($resultado[fpago] != "Tarjeta credito")&& ($resultado[fpago] != "Liquidación"))
		//$pdf->ezText("CC:".$resultado[1]);
		$pdf->ezText($resultado[1]." ".$resultado[2]);
		// Agregamos si existe en Pedido de Cliente
		if ( !is_null( $resultado['pedidoCliente'] ) ) {
			$pdf->ezText( $resultado['pedidoCliente'] );
		}

//Si se ha mandado a guardar escribimos en el fichero
		if(isset($_POST[factura]))
		{
			$pdfcode = $pdf->output();
			$nombre_factura = "factura_".$factura.".pdf";
			$ruta_osx = "/Users/ruben/Desktop/facturas/";
			$ruta_wxp = "\\\\172.26.0.131\RED\PLANTILLAS\facturas\\";
			if(isset($_POST[envio]))
				$ruta = "tmp/".$nombre_factura;
			else
				$ruta = $ruta_wxp.$nombre_factura;
			//$ruta_wxp = "C:\RED\PLANTILLAS\facturas\\";
			$fp=fopen($ruta,'wb');
			fwrite($fp,$pdfcode);
			fclose($fp);
			unset($pdfcode);
			
			if(isset($_POST[envio]))
			{
				include("envio.php");
			}
		}
		else
			$pdf->ezStream();
}*/
?>