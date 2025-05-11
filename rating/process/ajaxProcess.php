<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/app/plugin/rating/main.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/app/plugin/rating/include/rating_function.php');

use App\Plugin\Rating\Rating;

if (checkAjaxRequest()) {

    $_POST = cleanRequest($_POST);

    if (isset($_POST['fetch']) && !empty($_POST['widget_type']) && !empty($_POST['widget_id'])) {
        $Rating = new Rating($_POST['widget_type'], $_POST['widget_id']);

        $data = getRate($Rating->getData());
        $data['widget_id'] = $_POST['widget_type'] . '-item-' . $_POST['widget_id'];

        echo json_encode($data);
    }

    if (isset($_POST['clicked_on']) && !empty($_POST['widget_type']) && !empty($_POST['widget_id'])) {

        preg_match('/star_([1-5]{1})/', $_POST['clicked_on'], $match);

        $Rating = new Rating($_POST['widget_type'], $_POST['widget_id']);
        $Rating->setUser(time());
        $Rating->setScore($match[1]);

        if ($Rating->notExist()) {
            if ($Rating->save()) {

                /*$data = getRate($Rating->getData());
                $data['widget_id'] = 'item-' . $_POST['widget_id'];

                echo json_encode($data);*/

                echo json_encode(trans('Merci'));
            }
        } else {
            echo json_encode(trans('Vous avez déjà voté'));
        }
    }

    if (isset($_POST['initRating']) && !empty($_POST['type']) && !empty($_POST['typeId'])) {

        $Rating = new Rating();
        $Rating->setType($_POST['type']);
        $Rating->setTypeId($_POST['typeId']);

        if ($Rating->deleteAll()) {
            echo 'true';
        }
    }

    if (isset($_POST['confirmRating']) && !empty($_POST['idRating'])) {

        $Rating = new Rating();
        $Rating->setId($_POST['idRating']);
        $Rating->show();
        $Rating->setStatus(1);

        if ($Rating->update()) {
            echo 'true';
        }
    }
}