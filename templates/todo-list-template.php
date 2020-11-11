<?php

/* Template Name: Todo List Template */

get_header(); ?>

	<main id="primary" class="site-main todos-page-wrapper">
    
<?php
$term_list = get_terms([
	'taxonomy' => 'todos_categories',
	'hide_empty' => false,
]);

$event_cats = '';

foreach ($term_list as $term_single) {
	$term_link = get_term_link($term_single);
	$term_name = $term_single->name;
	$term_slug = $term_single->slug;

	$term = "<li><button data-slug='{$term_slug}' class='student-category'>{$term_name}</button></li>";
	$event_cats .= $term;
}

$event_cats .= "<li><button class='student-category-all'>See All</button></li>";

echo "
  <nav class='category-buttons-wrapper' aria-labelledby='event-category-label'>
  <ul class='category-buttons'>
  <p id='event-category-label'>Show todos in category:</p>
  {$event_cats}</ul>
  </nav>
  ";
?>    

		<?php while (have_posts()):
  	the_post();

  	get_template_part('template-parts/content', get_post_type());

  	the_post_navigation([
  		'prev_text' => '<span class="nav-subtitle">' . esc_html__('Previous:', 'sights') . '</span> <span class="nav-title">%title</span>',
  		'next_text' => '<span class="nav-subtitle">' . esc_html__('Next:', 'sights') . '</span> <span class="nav-title">%title</span>',
  	]);

  	// If comments are open or we have at least one comment, load up the comment template.
  	if (comments_open() || get_comments_number()):
  		comments_template();
  	endif;
  endwhile;
// End of the loop.
?>

	</main><!-- #main -->

<?php
get_sidebar();
get_footer();
?>

<script>

let all_checkbox_inputs = document.querySelectorAll('input[type="checkbox"]');

let buttons = document.querySelectorAll('.student-category')

let todos = document.querySelectorAll('.todo-wrapper')




let event_categories_to_display = localStorage.getItem('all_categories') ?  localStorage.getItem('all_categories').split(",") : [];
  


handle_checkbox_clicks = () => {
  localStorage.setItem('all_checkboxes', '')  

  let all_checkbox_inputs_obj = {}

  all_checkbox_inputs.forEach((input)=>{
    all_checkbox_inputs_obj[input.id] = input.checked
  })
  
  localStorage.setItem('all_checkboxes', JSON.stringify(all_checkbox_inputs_obj))  
  
}

handle_button_clicks = (el) => {

  localStorage.setItem('all_categories', '')  
  
  display_relevant_todos = (callback)=>{
    todos.forEach((todo)=>{
      todo.classList.remove('todo-enabled');
    }) 
    event_categories_to_display.forEach((cat)=>{  
      document.querySelectorAll(`.category-${cat}`).forEach((todo)=>{
        todo.classList.add('todo-enabled')
      })
    }) 
  }  

  if (!event_categories_to_display.includes(el.target.dataset.slug)) {
    // adds the item to the array if it's not there
    event_categories_to_display.push(el.target.dataset.slug)  
    display_relevant_todos()
  } else {
    
    // removes the item from the array if it's there
    const index = event_categories_to_display.indexOf(el.target.dataset.slug);
        
    if (index > -1) {
      event_categories_to_display.splice(index, 1);
    }
    
    display_relevant_todos()
    
  }
  
  localStorage.setItem('all_categories', event_categories_to_display)
  
}

handle_see_all_button_click = () => {
  todos.forEach((todo)=>{
    todo.classList.add('todo-enabled');
  })
  event_categories_to_display = []
  localStorage.setItem('all_categories', '')

}

restore_all = () => {
  let checks = JSON.parse(localStorage.getItem('all_checkboxes'));
    
  let categories = localStorage.getItem('all_categories');

  if (checks != null) {

    for (let [key, value] of Object.entries(checks)) {

      let elm = document.getElementById(`${key}`)
      
      if (elm !== 'undefined') {
        if (elm.checked == false && value == true) {
          elm.checked = value
          elm.dispatchEvent(new Event('click', { bubbles: true }))
        } else {
          elm.checked = value
        }
      }
    }
  }
  
  if (categories != null) {
    
    categories = categories.split(",");
    categories.forEach((cat)=>{
      let els = document.querySelectorAll(`.category-${cat}`)
      
      els.forEach((el)=>{
        el.classList.add('todo-enabled');
      })
    })
  }
}

all_checkbox_inputs.forEach((input)=>{
  input.addEventListener('click',  handle_checkbox_clicks)
})

buttons.forEach((el)=>{
  el.addEventListener('click', handle_button_clicks)
})

window.addEventListener('DOMContentLoaded', (event) => {
  
  if (localStorage.getItem('all_categories') == "undefined" || localStorage.getItem('all_categories') == "" ) {
    
    todos.forEach((todo)=>{
      todo.classList.add('todo-enabled');
    }) 
    
  }

  restore_all()
})

document.querySelector('.student-category-all').addEventListener('click', handle_see_all_button_click);

let all_expanders = document.querySelectorAll('.todo-expand');

all_expanders.forEach((el)=>{
  el.addEventListener('click', (event)=>{
    
    event.target.parentNode.parentNode.querySelector(".content-wrapper").classList.toggle('visible')
  })
})



	
</script>
