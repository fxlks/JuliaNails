<?php
get_header(); // Loads header.php

if ( have_posts() ) : 
    while ( have_posts() ) : the_post(); 
        the_content(); // This is what pulls in your Gutenberg/Kadence blocks
    endwhile; 
endif; 

get_footer(); // Loads footer.php
?>