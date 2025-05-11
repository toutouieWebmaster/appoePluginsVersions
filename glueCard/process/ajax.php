<?php

use App\DB;
use App\Plugin\GlueCard\Content;
use App\Plugin\GlueCard\Handle;
use App\Plugin\GlueCard\Item;
use App\Plugin\GlueCard\Plan;

require_once(dirname(__DIR__) . '/main.php');
if (checkAjaxRequest()) {

    if (getUserIdSession()) {

        $_POST = cleanRequest($_POST);

        /** HANDLE */
        if (!empty($_POST['newHandleName'])) {
            $Handle = new Handle();
            $Handle->setName($_POST['newHandleName']);
            if ($return = $Handle->save()) {
                $lastInsertId = DB::lastInsertId();
                echo $lastInsertId;
                exit();
            }
        }

        if (!empty($_POST['updateHandleName']) && !empty($_POST['updateHandleId'])) {
            $Handle = new Handle($_POST['updateHandleId']);
            $Handle->setName($_POST['updateHandleName']);
            if ($Handle->update()) {
                echo 'true';
                exit();
            }
        }

        if (!empty($_POST['archiveHandleId']) && is_numeric($_POST['archiveHandleId'])) {
            $Handle = new Handle($_POST['archiveHandleId']);
            $Handle->setStatus(0);
            if ($Handle->update()) {
                echo 'true';
                exit();
            }
        }

        if (!empty($_POST['deleteHandleId']) && is_numeric($_POST['deleteHandleId'])) {
            $Handle = new Handle();
            $Handle->setId($_POST['deleteHandleId']);
            if ($Handle->delete()) {
                echo 'true';
                exit();
            }
        }

        if (!empty($_POST['unpackHandleId']) && is_numeric($_POST['unpackHandleId'])) {
            $Handle = new Handle($_POST['unpackHandleId']);
            $Handle->setStatus(1);
            if ($Handle->update()) {
                echo 'true';
                exit();
            }
        }

        /** PLAN */
        if (!empty($_POST['newPlanHandleId']) && !empty($_POST['newPlanName'])
            && !empty($_POST['newPlanType'])) {
            $Plan = new Plan();
            $Plan->setIdHandle($_POST['newPlanHandleId']);
            $Plan->setName($_POST['newPlanName']);
            $Plan->setType($_POST['newPlanType']);
            if ($Plan->save()) {
                echo 'true';
                exit();
            }
        }

        if (!empty($_POST['updatePlanId']) && !empty($_POST['updatePlanName'])
            && is_numeric($_POST['updatePlanId'])) {
            $Plan = new Plan($_POST['updatePlanId']);
            $Plan->setName($_POST['updatePlanName']);
            if ($Plan->update()) {
                echo 'true';
                exit();
            }
        }

        if (!empty($_POST['removePlanId']) && is_numeric($_POST['removePlanId'])) {
            $Plan = new Plan();
            $Plan->setId($_POST['removePlanId']);
            if ($Plan->delete()) {
                echo 'true';
                exit();
            }
        }

        /** ITEM */
        if (!empty($_POST['newItemHandleId']) && is_numeric($_POST['newItemHandleId'])) {
            $Item = new Item();
            $Item->setIdHandle($_POST['newItemHandleId']);
            if ($Item->save()) {
                echo 'true';
                exit();
            }
        }

        if (!empty($_POST['archiveItemId']) && is_numeric($_POST['archiveItemId'])) {
            $Item = new Item($_POST['archiveItemId']);
            $Item->setStatus(0);
            if ($Item->update()) {
                echo 'true';
                exit();
            }
        }

        if (!empty($_POST['deleteItemId']) && is_numeric($_POST['deleteItemId'])) {
            $Item = new Item();
            $Item->setId($_POST['deleteItemId']);
            if ($Item->delete()) {
                echo 'true';
                exit();
            }
        }

        if (!empty($_POST['newItemOrder']) && is_numeric($_POST['newItemOrder'])
            && !empty($_POST['itemId']) && is_numeric($_POST['itemId'])) {
            $Item = new Item($_POST['itemId']);
            $Item->setOrder($_POST['newItemOrder']);
            if ($Item->update()) {
                echo 'true';
                exit();
            }
        }

        if (!empty($_POST['unpackItemId']) && is_numeric($_POST['unpackItemId'])) {
            $Item = new Item($_POST['unpackItemId']);
            $Item->setStatus(1);
            if ($Item->update()) {
                echo 'true';
                exit();
            }
        }

        /** CONTENT */
        if (!empty($_POST['contentHandleId']) && !empty($_POST['contentPlanId']) && !empty($_POST['contentItemId'])
            && is_numeric($_POST['contentHandleId']) && is_numeric($_POST['contentPlanId']) && is_numeric($_POST['contentItemId'])
            && isset($_POST['contentText'])) {
            $Content = new Content();
            $Content->setIdHandle($_POST['contentHandleId']);
            $Content->setIdPlan($_POST['contentPlanId']);
            $Content->setIdItem($_POST['contentItemId']);
            $Content->setText($_POST['contentText']);
            if (!$Content->exist()) {
                if ($Content->save()) {
                    echo 'true';
                    exit();
                }
            } else {
                if ($Content->update()) {
                    echo 'true';
                    exit();
                }
            }
        }
    }
}