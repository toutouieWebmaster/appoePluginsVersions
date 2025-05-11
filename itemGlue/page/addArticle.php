<?php
require( 'header.php' );
require( ITEMGLUE_PATH . 'process/postProcess.php' );

use App\Form;

echo getTitle( getAppPageName(), getAppPageSlug() );
showPostResponse( getDataPostResponse() ); ?>
    <form action="" method="post" id="addArticleForm">
		<?= getTokenField(); ?>
        <div class="row my-2">
            <div class="col-12 col-lg-6 my-2">
				<?= Form::text( 'Nom', 'name', 'text', ! empty( $_POST['name'] ) ? $_POST['name'] : '', true, 70, 'autofocus data-seo="title"' ); ?>
                <div class="mt-3">
					<?= Form::text( 'Nom du lien URL (slug)', 'slug', 'text', ! empty( $_POST['slug'] ) ? $_POST['slug'] : '', true, 70, 'data-seo="slug"' ); ?>
                </div>
            </div>
            <div class="col-12 col-lg-6 my-2">
				<?= Form::textarea( 'Description', 'description', ! empty( $_POST['description'] ) ? $_POST['description'] : '', 4, true, 'maxlength="158" data-seo="description"' ); ?>
            </div>
            <div class="col-12 p-3">
				<?= Form::radio( 'Statut de l\'article', 'statut', array_map( 'trans', ITEMGLUE_ARTICLES_STATUS ), ! empty( $_POST['statut'] ) ? $_POST['statut'] : 1, true ); ?>
            </div>
            <div class="col-12 mb-3">
				<?= Form::target( 'ADDARTICLE' ); ?>
				<?= Form::submit( 'Enregistrer', 'ADDARTICLESUBMIT', 'btn-outline-info' ); ?>
            </div>
        </div>
    </form>
<?php require( 'footer.php' ); ?>