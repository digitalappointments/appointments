function handleOver(image_name) {
  if (image_name != active_page) {
    if (document.images && (main_menu_buttons[image_name])) {
      document[image_name].src=main_menu_buttons[image_name]['hover'].src;
    }
  }
}

function handleOut(image_name) {
  if (image_name != active_page) {
    if (document.images && (main_menu_buttons[image_name])) {
      document[image_name].src=main_menu_buttons[image_name]['normal'].src;
    }
  }
}

function signup() {
  // showPopWin("Sign Up", "/dialog/register/signup.php", 510, 270, null);
}

function signin() {
  showPopWin("Sign In", "/dialog/register/signin.php", 340, 210, null);
}
