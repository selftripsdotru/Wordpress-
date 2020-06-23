```<?php
function exclude_cat_from_previous_next_JOIN( $join = null, $in_same_cat = false, $excluded_categories = '' ) {
    if ( is_admin() ) {
        return $join;
    } else {
        global $wpdb;
        // NOTE: The p in p.ID is assigned from $wpdb-&gt;posts in the get_adjacent_post function.
        return $join." INNER JOIN $wpdb-&gt;term_relationships ON 
                      (p.ID = $wpdb-&gt;term_relationships.object_id) 
                      INNER JOIN $wpdb-&gt;term_taxonomy ON 
                      ($wpdb-&gt;term_relationships.term_taxonomy_id = $wpdb-&gt;term_taxonomy.term_taxonomy_id)";			
    }
}
add_filter( 'get_next_post_join', 'exclude_cat_from_previous_next_JOIN', 10, 3 );
add_filter( 'get_previous_post_join', 'exclude_cat_from_previous_next_JOIN', 10, 3 );
	
function exclude_cat_from_previous_next_WHERE( $where = null, $in_same_cat = false, $excluded_categories = '' ) {
    if ( is_admin() ) {
        return $where;
    } else {
        global $wpdb;;
        $exclude = '681, 682, 683, 684'; //The IDs of the categories to exclude.
// Разобьем строку на массив
$exclude_array = explode( ',', $exclude );
// Уберем пробелы в каждом элементе
$exclude_array = array_map( 'trim', $exclude_array );
// Тут получим текущий пост
global $post;
// массив объектов его категорий
$my_post_id=get_the_ID( ); 
$cats = get_the_category( $post-&gt;ID );
// Создадим массив для id категорий
$cats_array = array();
// Заполним массив id
foreach( $cats as $category ) {
  array_push( $cats_array, $category-&gt;cat_ID );
}
// Удаляем из массива "сключенных категорий" категории в которых содержится текущий пост
$result = array();
$result = array_diff ($exclude_array, $cats_array);
//создаем строковую переменную из элементов массива $result, с разделенными точкой и пробелами элементами
$result2='';
foreach ($result as &amp;$value) {
    $result2 .= "$value";
	$result2 .= ', ';
}
		$result2 = rtrim($result2, ", ");
        $result2 = apply_filters( 'exclude_cat_from_previous_next_WHERE_filter', $result2 );

        return $where." AND $wpdb-&gt;term_taxonomy.taxonomy = 'category' 
                        AND $wpdb-&gt;term_taxonomy.term_id NOT IN ($result2)";
    }
}
add_filter( 'get_next_post_where', 'exclude_cat_from_previous_next_WHERE', 10, 3);
add_filter( 'get_previous_post_where', 'exclude_cat_from_previous_next_WHERE', 10, 3);
?>
