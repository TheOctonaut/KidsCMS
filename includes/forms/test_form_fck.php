<?php
require('forms.php');

Function OutputDebug($error)
{
	echo "$error\n";
}

	require("forms.php");
	require("form_FCKEditor.php");

	$form=new form_class;
	$form->NAME="FCKEditor_form";
	$form->METHOD="POST";
	$form->ACTION="";
	$form->debug="OutputDebug";
	$form->WIDTH="100%";
	$form->AddInput(array(
		"TYPE"=>"custom",
		"ID"=>"FCK",
		"CustomClass"=>"form_FCKEditor",
		"VALUE"=>'This is some <strong>sample text</strong>. You are using <a href="http://www.fckeditor.net/">FCKeditor</a>.',
		"BasePath"=>"/FCKEditor/",
		"HEIGHT"=>300,
		"Skin"=>"silver",
		"LABEL"=>"Play around with your html",
		"ONCOMPLETE"=>"alert('FCKEditor completed loading')"
	));
	$form->AddInput(array(
		"TYPE"=>"submit",
		"VALUE"=>"Save HTML",
		"NAME"=>"doit"
	));
	$form->LoadInputValues($form->WasSubmitted("doit"));
	$verify=array();
	if($form->WasSubmitted("doit"))
	{
		if(($error_message=$form->Validate($verify))=="")
			$doit=1;
		else
		{
			$doit=0;
			$error_message=HtmlEntities($error_message);
		}
	}
	else
	{
		$error_message="";
		$doit=0;
	}
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<head>
<title>Test for Mat&iacute;as Montes' PHP FCKEditor plug-in input for Manuel Lemos' forms class </title>
<?php echo $form->PageHead(); ?>
</head>
<body bgcolor="#cccccc">
<center><h1>Test for Mat&iacute;as Montes' PHP FCKEditor plug-in input for Manuel Lemos' forms class</h1></center>
<hr />
<?php
  if($doit)
	{
		$form->SetInputProperty("FCK","Accessible",0);
	}

	$form->StartLayoutCapture();
	$title="Form FCKEditor plug-in test";
	include("templates/form_fck_body.html.php");
	$form->EndLayoutCapture();

	$form->DisplayOutput();

?>
<hr />
</body>
</html>
