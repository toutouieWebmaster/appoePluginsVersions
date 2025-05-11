<?php

use App\Category;
use App\CategoryRelations;
use App\Plugin\ItemGlue\Article;
use App\Plugin\ItemGlue\ArticleMedia;
use App\Plugin\ItemGlue\ArticleMeta;
use App\Plugin\ItemGlue\ArticleRelation;

/**
 * @param object|int $articleId
 * @param string $lang
 * @return Article|bool
 */
function getArticlesDataById($articleId, $lang = LANG)
{
    if (is_numeric($articleId)) {
        $Article = new Article($articleId, $lang);

    } elseif (is_object($articleId)) {
        $Article = $articleId;
    } else {
        return false;
    }

    //get article content
    $Article->setContent(htmlSpeCharDecode($Article->getContent()));

    //Get Media
    $ArticleMedia = new ArticleMedia($Article->getId());
    $Article->setMedias($ArticleMedia->showFiles());

    //Get Metas
    $ArticleMeta = new ArticleMeta($Article->getId(), $Article->getLang());
    $metas = $ArticleMeta->getData() ? extractFromObjToSimpleArr($ArticleMeta->getData(), 'metaKey', 'metaValue') : [];
    $Article->setMetas($metas);

    //get all categories in relation with article
    $Article->setCategoriesDetails(getCategoriesByArticle($Article->getId()));
    $Article->setCategories(extractFromObjToSimpleArr($Article->getCategoriesDetails(), 'categoryId', 'name'));

    return $Article;
}

/**
 * @param stdClass $article
 * @return stdClass
 */
function getArticleData(stdClass $article)
{

    //Get Media
    $ArticleMedia = new ArticleMedia($article->id);
    $article->medias = $ArticleMedia->showFiles();

    //Get Metas
    $ArticleMeta = new ArticleMeta($article->id);
    $article->metas = $ArticleMeta->getData() ? extractFromObjToSimpleArr($ArticleMeta->getData(), 'metaKey', 'metaValue') : [];

    //get all categories in relation with article
    $article->categoriesDetails = getCategoriesByArticle($article->id);
    $article->categories = extractFromObjToSimpleArr($article->categoriesDetails, 'categoryId', 'name');

    return $article;
}

/**
 * @param int $categoryId
 * @param bool $parent
 * @param bool|int $length
 * @param string $lang
 * @return array|bool
 */
function getArticlesByCategory($categoryId, $parent = false, $length = false, $lang = LANG)
{
    $Article = new Article();
    $allArticles = $Article->showByCategory($categoryId, $parent, $lang);

    if (!$allArticles) return false;

    foreach ($allArticles as &$article) {
        $article = getArticleData($article);
    }

    return $length ? array_slice($allArticles, 0, $length, true) : $allArticles;
}


/**
 * @param bool $length
 * @param string $lang
 * @return array|bool
 */
function getRecentArticles($length = false, $lang = LANG)
{
    $Article = new Article();
    $allArticles = $Article->showAllByLang($length, $lang);

    if (!$allArticles) return false;

    foreach ($allArticles as &$article) {
        $article = getArticleData($article);
    }

    return $allArticles;
}

/**
 * @param string $searching
 * @return array|bool
 */
function getSearchingArticles($searching)
{
    $searching = cleanData($searching);

    $Article = new Article();
    $allArticles = $Article->searchFor($searching);

    if (!$allArticles) return false;

    foreach ($allArticles as &$article) {
        $article = getArticleData($article);
    }

    return $allArticles;
}

/**
 * @param $articleId
 * @param $categories
 * @param bool $length
 * @return array
 */
function getSimilarArticles($articleId, $categories, $length = false)
{
    $relatedArticles = [];
    $allArticles = [];

    if (is_numeric($categories)) {
        $relatedArticles[$categories] = unsetSameKeyInArr(extractFromObjArr(getArticlesByCategory($categories, true), 'id'), $articleId);

    } elseif (is_array($categories)) {
        foreach ($categories as $key => $category) {
            $relatedArticles[$key] = unsetSameKeyInArr(extractFromObjArr(getArticlesByCategory($key, true), 'id'), $articleId);
        }
    }

    foreach ($relatedArticles as $categoryId => $articles) {
        foreach ($articles as $articleId => $article) {

            if (!array_key_exists($articleId, $allArticles)) {
                $allArticles[$articleId] = $article;
            }
        }
    }

    return $length ? array_slice($allArticles, 0, $length, true) : $allArticles;

}

/**
 * @param $id
 * @param $idCategory
 * @param $parent
 * @return Article|bool
 */
function getNextArticle($id, $idCategory = false, $parent = false)
{

    if (is_numeric($id)) {

        $Article = new Article();
        $Article->setId($id);
        if ($Article->showNextArticle(LANG, $idCategory, $parent)) {
            return getArticlesDataById($Article->getId());
        }
    }

    return false;
}

/**
 * @param $id
 * @param $idCategory
 * @param $parent
 * @return Article|bool
 */
function getPreviousArticle($id, $idCategory = false, $parent = false)
{

    if (is_numeric($id)) {

        $Article = new Article();
        $Article->setId($id);
        if ($Article->showPreviousArticle(LANG, $idCategory, $parent)) {
            return getArticlesDataById($Article->getId());
        }
    }

    return false;
}

/**
 * @param $year
 * @param bool $month
 * @param int $status
 * @param int|bool $length
 * @param string $lang
 * @param int|bool $idCategory
 * @param bool $parentCategory
 * @return array|bool
 */
function getArticlesArchives($year, $month = false, $status = 1, $length = false, $lang = LANG, $idCategory = false, $parentCategory = false)
{
    if (is_numeric($year)) {

        $Article = new Article();
        $Article->setStatut($status);
        $allArticles = $Article->showArchives($year, $month, $length, $lang, $idCategory, $parentCategory);

        if (!$allArticles) return false;

        foreach ($allArticles as $key => &$article) {

            $article = getArticleData($article);
        }

        return $allArticles;
    }
    return false;
}

/**
 * @param $categoryId
 * @param bool $parentId
 * @param int $favorite
 * @param bool|array $archives
 * @return mixed
 */
function getSpecificArticlesCategory($categoryId, $parentId = false, $favorite = 1, $archives = false)
{
    //get all articles categories
    $Category = new Category();
    $Category->setType('ITEMGLUE');
    $allCategories = extractFromObjArr($Category->showByType(), 'id');

    //get all articles
    $Article = new Article();
    $Article->setStatut($favorite);
    $allArticles = !$archives ? extractFromObjArr($Article->showAll(), 'id') : extractFromObjArr($Article->showArchives($archives['year'], $archives['month']), 'id');

    //get all categories in relation with all articles
    $CategoryRelation = new CategoryRelations();
    $CategoryRelation->setType('ITEMGLUE');
    $allCategoriesRelations = extractFromObjArr($CategoryRelation->showAll(), 'id');

    $all['articles'] = [];
    $all['categories'] = [];
    $all['countCategories'] = [];

    if ($allCategoriesRelations) {

        //search only in categories relations
        foreach ($allCategoriesRelations as $relationId => $categoryRelation) {

            //check parent Id and Id
            if (false !== $parentId) {

                if ($allCategories[$categoryRelation->categoryId]->parentId != $categoryId) {
                    continue;
                }

            } else {
                if ($categoryRelation->id != $categoryId) {
                    continue;
                }
            }

            //count categories
            if (!array_key_exists($allCategories[$categoryRelation->categoryId]->name, $all['countCategories'])) {
                $all['countCategories'][$allCategories[$categoryRelation->categoryId]->name] = 0;
            }

            $all['countCategories'][$allCategories[$categoryRelation->categoryId]->name] += 1;

            if (array_key_exists($categoryRelation->typeId, $allArticles)) {

                //push into Articles
                if (!array_key_exists($categoryRelation->typeId, $all['articles'])) {
                    $all['articles'][$categoryRelation->typeId] = $allArticles[$categoryRelation->typeId];
                    $all['categories'][$categoryRelation->typeId] = [];
                }

                //push into categories
                if (!in_array($categoryRelation->categoryId, $all['categories'][$categoryRelation->typeId])) {
                    $all['categories'][$categoryRelation->typeId][] = $allCategories[$categoryRelation->categoryId]->name;
                }
            }
        }
    }

    return $all;
}

/**
 * Obsolète function
 * @param $slug
 * @return bool|array
 */
function getSpecificArticlesDetailsBySlug($slug)
{
    if (!empty($slug)) {

        $slug = trad($slug, true);

        //get article
        $Article = new Article();
        $Article->setSlug($slug);
        if ($Article->showBySlug()) {

            //get all categories in relation with article
            $CategoryRelation = new CategoryRelations('ITEMGLUE', $Article->getId());
            $allCategoriesRelations = $CategoryRelation->getData();

            //get article metas
            $ArticleMeta = new ArticleMeta($Article->getId());
            $allArticleMeta = $ArticleMeta->getData();

            //get article medias
            $ArticleMedia = new ArticleMedia($Article->getId());
            $allArticleMedia = $ArticleMedia->showFiles();

            $all['article'] = $Article;
            $all['content'] = $Article->getContent();
            $all['meta'] = $allArticleMeta;
            $all['categories'] = $allCategoriesRelations;
            $all['media'] = $allArticleMedia;

            return $all;
        }
    }
    return false;
}


/**
 * @param $slug
 * @return Article|bool
 */
function getArticlesBySlug($slug)
{
    if (!empty($slug)) {

        //get article
        $Article = new Article();
        $Article->setSlug($slug);
        if ($ArticleBySlug = $Article->getBySlug()) {
            return getArticlesDataById($ArticleBySlug->idArticle);
        }
    }
    return false;
}

/**
 * @param $articleId
 * @return null
 */
function getCategoriesByArticle($articleId)
{

    //get all categories in relation with article
    $CategoryRelation = new CategoryRelations('ITEMGLUE', $articleId);
    return $CategoryRelation->getData();
}

/**
 * @param Article $Article
 * @param $parentId
 * @return array
 */
function getCategoriesInArticleByParent(App\Plugin\ItemGlue\Article $Article, $parentId)
{
    $categories = [];
    if (property_exists($Article, 'categoriesDetails')) {
        foreach ($Article->getCategoriesDetails() as $category) {
            if (is_object($category) && $category->parentId == $parentId) {
                $categories[$category->categoryId] = $category->name;
            }
        }
    }
    return $categories;
}

/**
 * @param $articles
 * @return array
 */
function getAllCategoriesInArticles($articles)
{
    $categories = [];

    if ($articles) {
        foreach ($articles as $article) {
            $cat = (str_contains($article->categoryNames, '||')) ? explode('||', $article->categoryNames) : $article->categoryNames;
            $categories[] = $cat;
        }
        $categories = flatten($categories);
    }
    return $categories;
}

/**
 * @param $article
 * @param $property
 * @return array
 */
function getCategoriesInArticle($article, $property = 'categoryNames')
{
    $categories = [];

    if ($article) {
        if (str_contains($article->$property, '||')) {
            $categories = explode('||', $article->$property);
        } else {
            $categories[] = $article->$property;
        }
    }
    return $categories;
}

/**
 * @param $article
 * @param $property
 * @return string
 */
function slugifyCategoriesInArticle($article, $property = 'categoryNames')
{
    $categories = '';

    if ($article) {
        if (str_contains($article->$property, '||')) {
            $categoriesArr = explode('||', $article->$property);
            foreach ($categoriesArr as &$cat) {
                $cat = slugify($cat);
            }
            $categories = implode('||', $categoriesArr);
        } else {
            $categories = slugify($article->$property);
        }
    }
    return $categories;
}

/**
 * @param string $categories
 * @param string $separator
 * @return string
 */
function getSlugifyCategories(string $categories, $separator = '||')
{

    if (str_contains($categories, $separator)) {
        $categories = explode($separator, $categories);
        $categories = array_map('slugify', $categories);
        return implode(' ', $categories);
    }
    return slugify($categories);
}

/**
 * @param $Article
 * @param array|string $categories
 * @return bool
 */
function articleHasCategories($Article, $categories)
{
    if (property_exists($Article, 'categories')) {
        if (is_array($categories)) {
            foreach ($categories as $category) {
                if (array_key_exists($category, $Article->categories)) {
                    return true;
                }
            }
        } else {
            return array_key_exists($categories, $Article->categories);
        }
    }
    return false;
}

/**
 * @param stdClass $Article
 * @param string $meta
 * @param string $page
 * @return string
 */
function getArticleUrl(stdClass $Article, $meta = 'link', $page = '')
{

    if (!empty($Article->metas[$meta])) {

        /*
         * If (meta "link" contains "http") return "link"
         * Else return the website url with "link"
         */
        return str_contains($Article->metas[$meta], 'http') ? $Article->metas[$meta] : webUrl($Article->metas[$meta] . DIRECTORY_SEPARATOR);
    }

    if (empty($page) && defined('DEFAULT_ARTICLES_PAGE')) {

        /*
         * Put Article in default articles page
         */
        $page = DEFAULT_ARTICLES_PAGE . DIRECTORY_SEPARATOR;
    }

    return articleUrl($Article->slug, $page);
}

/**
 * @param stdClass $Article
 * @param array $options
 * @return string|bool
 */function getArtFeaturedImg($Article, array $options = []): string|bool
{
    $options = array_merge([
        'tmpPos'     => 2,
        'forcedImg'  => true,
        'class'      => '',
        'thumbSize'  => false,
        'onlyUrl'    => false,
        'onlyPath'   => false,
        'webp'       => false
    ], $options);

    if (!is_object($Article)) {
        return false;
    }

    $medias = null;
    if (method_exists($Article, 'getMedias')) {
        $medias = $Article->getMedias();
    } elseif (property_exists($Article, 'medias')) {
        $medias = $Article->medias;
    }

    if ($medias === null) {
        return false;
    }

    return getFirstImage(
        getFileTemplatePosition($medias, $options['tmpPos'], $options['forcedImg']),
        $options['class'], $options['thumbSize'], $options['onlyUrl'],
        $options['onlyPath'], $options['webp']
    );
}


/**
 * @param object|array $article
 * @param $key
 * @return string
 */
function getArticleMeta($article, $key)
{
    if (is_object($article) && property_exists($article, 'metas')) {
        return !empty($article->metas[$key]) ? htmlSpeCharDecode($article->metas[$key]) : '';
    } elseif (is_array($article)) {
        return !empty($article[$key]) ? htmlSpeCharDecode($article[$key]) : '';
    }
    return '';
}

/**
 * @param $article
 * @param $key
 * @return bool
 */
function articleHasMeta($article, $key)
{
    if (is_object($article) && property_exists($article, 'metas')) {
        return !empty($article->metas[$key]);
    } elseif (is_array($article)) {
        return !empty($article[$key]);
    }
    return false;
}

/**
 * get article web url
 *
 * @param $articleSlug
 * @param $articlePage
 *
 * @return string
 */
function articleUrl($articleSlug, $articlePage = '')
{
    $articlePage = !empty($articlePage) ? $articlePage :
        (defined('DEFAULT_ARTICLES_PAGE') ? DEFAULT_ARTICLES_PAGE . DIRECTORY_SEPARATOR : '/');
    return webUrl($articlePage, $articleSlug);
}

/**
 * @param $articleId
 * @param string $type
 * @return array|bool
 */
function getArticleRelation($articleId, $type = 'USERS')
{

    if (!empty($articleId)) {
        $ArticleRelation = new ArticleRelation($articleId, $type);
        if ($ArticleRelation->getData()) {
            return extractFromObjToSimpleArr($ArticleRelation->getData(), 'id', 'typeId');
        }
    }

    return false;
}

/**
 * @param $articleId
 * @return array
 */
function getArticleUsers($articleId)
{
    $articleUsers = [];
    if (!empty($articleId)) {

        $articleRelations = getArticleRelation($articleId);
        if ($articleRelations) {

            foreach ($articleRelations as $relationId => $userId) {
                $articleUsers[$userId] = getUserEntitled($userId);
            }
        }
    }

    return $articleUsers;
}

/**
 * @param $articleId
 * @param $by = "Par"
 * @param $separator = "et"
 * @return string
 */
function showArticleUsers($articleId, $by = 'Par', $separator = 'et')
{
    $html = '';

    if (!empty($articleId)) {

        $articleUsers = getArticleUsers($articleId);
        if ($articleUsers) {

            $html = trans($by) . ' ';
            $count = 1;

            foreach ($articleUsers as $userId => $userEntitled) {
                $html .= ($count > 1 ? (' ' . trans($separator) . ' ') : '') . $userEntitled;
                $count++;
            }
        }
    }

    return $html;
}

/**
* @param $parentId
* @param $articles
* @return array|bool
 */
function getAllCategoriesInArticlesByPosition($parentId, $articles)
{
    $catsByPosition = [];
    $Article = new Article();
    $categories = $Article->showWithPosition($parentId);
    if (is_array($categories) && is_array($articles)) {
        foreach ($articles as $article) {
            foreach ($categories as $category) {
                if ($article == $category->name) {
                    $catsByPosition[$category->position] = $category->name;
                }
            }
        }
        ksort($catsByPosition);
        return $catsByPosition;
    }
    return false;
}