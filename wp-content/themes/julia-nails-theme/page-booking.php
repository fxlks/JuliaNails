
<?php get_header(); ?>


<?php 
       $bookingPageHeader = get_field('booking_page_header'); 
       $bookingPageSubheader = get_field('booking_page_subheader');


?>

<section class="bg-[#161413]">
        
<div class="mx-auto px-8 py-8">
    <h1 class="text-[#f2f2f2] text-4xl font-bold italic mb-5 text-center"><?php echo esc_html($bookingPageHeader); ?> </h1>
    <p class="text-[#f2f2f2] text-center text-xl italic"><?php echo esc_html($bookingPageSubheader); ?></p>
</div>

</section>




<?php get_footer(); ?>
