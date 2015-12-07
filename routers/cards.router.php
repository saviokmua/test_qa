<?php
use lib\Config;
// Get user
$app->get('/cards', function () use ($app) {	
	
	$card = new models\Card ();
	$cards = $card->getCards();
	$app->render('cards/index.html', array('cards' => $cards,'search' => @$_GET['search'],'active_cards'=>'active'));
	
});

$app->post('/card/create', function () use ($app) {	
	$data = $app->request()->post('generator');
	$card = new models\Card ();
	$card->create($data);
	$app->redirect('/cards');
	
});

$app->post('/card/update', function () use ($app) {	
	$data = $app->request()->post('card');
	$card = new models\Card ();
	$card->update($data);
	$app->redirect('/cards');
	
});


$app->get('/card/:id/destroy', function ($id) use ($app) {	
	$card = new models\Card ();
	$card->destroy($id);
	$app->redirect('/cards');
});

$app->get('/card/:id/edit', function ($id) use ($app) {	
	$card = new models\Card();
	$res=$card->getCardById($id);
	$card=$res[0];
	if ($card)
	{
		$card['checked']='';
		if ($card['status'] ==1)
			$card['checked']='checked="checked"';

		$app->render('cards/edit.html', array('card' => $card,'id'=>$id,'active_cards'=>'active'));
	}	
	else
	{
		$app->notFound();
	}
});

$app->get('/card/:id/view', function ($id) use ($app) {	
	$card = new models\Card();
	$purshare = new models\Purshare();
	$res=$card->getCardById($id);
	$c=$res[0];
	if ($c)
	{
		$c['status_name']=$card->getStatusName($id);
		
		$app->render('cards/profile.html', array('card' => $c,'id'=>$id,'active_cards'=>'active','purshares'=>$purshare->getPurshares($id)));
	}	
	else
	{
		$app->notFound();
	}
});

$app->get('/card/generator', function () use ($app) {	
	
	$app->render('cards/generator.html',array('active_cards'=>'active'));
	
});

$app->post('/card/check/number', function () use ($app) {	
	$data = $app->request()->post('generator');
	$card = new models\Card ();
	$app->contentType('application/json');
	echo json_encode($card->isNumber($data['number']));
});

$app->post('/card/check/serial', function () use ($app) {	
	$data = $app->request()->post('generator');
	$card = new models\Card ();
	$app->contentType('application/json');
	echo json_encode($card->isSerial($data['serial']));
});

