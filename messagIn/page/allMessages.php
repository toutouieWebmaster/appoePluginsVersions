<?php require( 'header.php' );
echo getTitle(getAppPageName(), getAppPageSlug()); ?>
    <div class="container-fluid">
        <?php $MessagIn = new \App\Plugin\MessagIn\MessagIn();
        $MessagIn->setToUser( getUserIdSession() );
        $allMessages = $MessagIn->showAll();
        $ALLUSERS    = getAllUsers();
        $counter     = 0;
        $displayList = ''; ?>
        <div class="row">
            <div class="col-12 col-sm-4 col-lg-3 col-xl-2 mb-4">
                <h2 class="h5 mb-3"><?= trans( 'Les utilisateurs' ); ?></h2>
                <div class="nav navUserMessages flex-column nav-pills" id="v-pills-tab" role="tablist">
                    <?php foreach ( $ALLUSERS as $userId => $user ):
                        if ( $userId != getUserIdSession() ): ?>
                            <a class="nav-link userMessages sidebarLink" id="v-pills-user-<?= $userId; ?>-tab"
                               data-toggle="pill"
                               href="#v-pills-user-<?= $userId; ?>"
                               role="tab" aria-controls="v-pills-user-<?= $userId; ?>"
                               aria-expanded="true"
                               data-iduser="<?= $userId; ?>"><?= $user->nom . ' ' . $user->prenom; ?> <span
                                        class="nbMessageSpan badge badge-light"></span></a>
                        <?php endif;
                    endforeach; ?>
                </div>
            </div>

            <div class="col-12 col-sm-8 col-lg-9 col-xl-10 mb-4">
                <h2 class="h5 mb-3"><?= trans( 'Les messages' ); ?></h2>
                <?php if ( $allMessages ): ?>
                    <div class="tab-content" id="v-pills-tabContent">
                        <?php foreach ( $ALLUSERS as $userId => $user ):
                            $counter = 0;
                            if ( $userId != getUserIdSession() ): ?>
                                <div class="tab-pane fade show msgContainer" id="v-pills-user-<?= $userId; ?>"
                                     role="tabpanel" aria-labelledby="v-pills-user-<?= $userId; ?>-tab">
                                    <div class="list-group" data-iduser="<?= $userId; ?>">
                                        <?php foreach ( $allMessages as $message ):
                                            if ( ! $message ): ?>
                                                <div class="list-group-item list-group-item-action">
                                                    <p class="p-3"><?= trans( 'Pas de messages' ); ?></p>
                                                </div>
                                            <?php else:
                                                if ( $message->fromUser == $userId ):
                                                    $counter ++;
                                                    $displayList = ( $counter >= 11 ) ? 'tooMuchMessage' : 0;
                                                    ?>
                                                    <div class="list-group-item list-group-item-action <?= $displayList; ?> fileContent msgContent">
                                                        <button type="button" class="deleteBtn deleteMessage"
                                                                data-idmessage="<?= $message->id; ?>"
                                                                aria-label="Close" data-iduser="<?= $userId; ?>">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>

                                                        <div class="d-inline-block">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" <?= $message->statut ? 'checked' : ''; ?>
                                                                       class="custom-control-input changeStatut"
                                                                       data-idmessage="<?= $message->id; ?>"
                                                                       id="message<?= $message->id; ?>">
                                                                <label class="custom-control-label"
                                                                       for="message<?= $message->id; ?>"></label>
                                                            </div>
                                                        </div>
                                                        <div class="d-inline-block msgTextContainer">
                                                            <div>
                                                                <small><?= formatDateDiff( new DateTime( date( 'Y-m-d' ) ), new DateTime( $message->created_at ) ); ?></small>
                                                            </div>
                                                            <p class="mb-1"><?= htmlSpeCharDecode( $message->text ); ?></p>
                                                        </div>
                                                    </div>
                                                <?php endif;
                                            endif;
                                        endforeach; ?>
                                        <div class="nbMessage d-none"
                                             data-iduser="<?= $userId; ?>"><?= $counter; ?></div>
                                        <?php if ( $counter > 10 ): ?>
                                            <button class="list-group-item list-group-item-action seeMoreMessages">
                                                <?= trans( 'Voir tous' ); ?>
                                            </button>
                                        <?php elseif ( $counter == 0 ): ?>
                                            <p class="p-3"><?= trans( 'Pas de messages' ); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif;
                        endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="p-3"><?= trans( 'Pas de messages' ); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            let clickTimes = 0;
            countNbMessage();

            function removeOneOnUserNbMessage(idUser) {
                let $msgBadge = $('div.navUserMessages').find('a.userMessages[data-iduser="' + idUser + '"]').children('span.nbMessageSpan');
                let $msgContainer = $('div.msgContainer').find('div.list-group[data-iduser="' + idUser + '"]');
                let $nbMsgContainer = $msgContainer.children('div.nbMessage');
                let $nbMsgBadge = $msgBadge.text();
                let $nbMsgCounter = $nbMsgContainer.text();
                $msgBadge.text($nbMsgBadge - 1);
                $nbMsgContainer.text($nbMsgCounter - 1);

                if ($nbMsgCounter == 1) {
                    $msgBadge.remove();
                } else if ($nbMsgCounter <= 11) {
                    $msgContainer.find('div.msgContent').removeClass('tooMuchMessage');
                }
            }

            function countNbMessage() {
                $('.nbMessage').each(function () {
                    let $countMsg = $(this);
                    let nbMessage = parseFloat($countMsg.text());
                    if (nbMessage > 0) {
                        let idUser = $countMsg.data('iduser');
                        $('div.navUserMessages').find('a.userMessages[data-iduser="' + idUser + '"]').children('span.nbMessageSpan').text(nbMessage);
                        if (nbMessage <= 10) {
                            $countMsg.parent().children('.seeMoreMessages').remove();
                        }
                    }
                });
            }

            $('.seeMoreMessages').unbind().click(function () {
                clickTimes++;
                if (clickTimes == 2) {
                    clickTimes = 0;
                    $(this).html('Voir plus');
                    $('.tooMuchMessage').fadeOut('fast');
                } else {
                    $(this).html('Voir moins');
                    $('.tooMuchMessage').fadeIn('fast');
                }

            });

            $('.deleteMessage').on('click', function (event) {
                event.preventDefault();
                event.stopPropagation();
                let $parent = $(this).parent('div');
                let idMessage = $(this).data('idmessage');
                let idUser = $(this).data('iduser');

                if (confirm('<?= trans( 'Vous allez supprimer ce message' ); ?>')) {
                    $.post(
                        '<?= MESSAGERIE_URL . 'process/ajaxProcess.php'; ?>',
                        {
                            idMessageToDelete: idMessage
                        },
                        function (data) {
                            if (data === true || data === 'true') {
                                $parent.remove();
                                removeOneOnUserNbMessage(idUser);
                                countNbMessage();
                            }
                        }
                    );
                }
            });

            $('.changeStatut').on('click', function () {

                let $input = $(this);
                $input.attr('disabled', 'disabled');

                let idMessage = $input.data('idmessage');
                let statut = 0;
                if ($input.is(':checked')) {
                    statut = 1;
                }

                setTimeout(function () {
                    $.post(
                        '<?= MESSAGERIE_URL . 'process/ajaxProcess.php'; ?>',
                        {
                            idMessageTochangeStatut: idMessage,
                            statutMessage: statut
                        },
                        function (data) {
                            if (data !== true && data != 'true') {
                                alert('Un problème est survenu !')
                            } else {
                                $input.attr('disabled', false);
                            }
                        }
                    );
                }, 1000);
            });
        });
    </script>
<?php require( 'footer.php' ); ?>