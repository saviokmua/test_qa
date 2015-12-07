<?php
use lib\Config;
// Get user

$app->get('/purshares', function () use ($app) {	
	
	$purshare = new models\Purshare ();
	$card = new models\Card ();

	$purshares = $purshare->getPurshares();
	foreach ($purshares as $key => $value) {
		$c=$card->getCardById($value['card_id']);
		$purshares[$key]['card']=$c[0]['number'].' / '.$c[0]['serial'];
	}

	$cards = $card->getCards();
	$app->render('purshares/index.html', array('purshares' => $purshares,'cards' => $cards,'active_purshares' => 'active'));
});

$app->post('/purshare/create', function () use ($app) {	
	$data = $app->request()->post('purshare');
	$purshare = new models\Purshare ();
	$purshare->create($data);
	$app->redirect('/purshares');
	
});

$app->get('/purshare/:id/destroy', function ($id) use ($app) {	
	$purshare = new models\Purshare ();
	$purshare->destroy($id);
	$app->redirect('/purshares');
});

$route="purshare";
