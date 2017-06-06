<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once __DIR__."/../avatar.php";

class CW_LS_UserProfile {  
  static $instance = null;
  public static function getInstance() {
    if(self::$instance === null) {
      self::$instance = new CW_LS_UserProfile();
    }
    return self::$instance;
  }
  private function __construct() {
    add_action( 'show_user_profile' , array( $this , 'show_lodestone_user_profile_fields' ) );
  }

  public function show_lodestone_user_profile_fields($user) {
    $user_data = get_user_meta($user->ID, 'character_profile', true);
    $mediaId = get_user_meta($id_or_email, 'avatar_media_id', true);
    $avatar_url = wp_get_attachment_image_url($mediaId);
    ?>

    <h3>Extra profile information</h3>

    <table class="form-table">

      <tr>
        <th><label for="character_data">Lodestone Character Profile Data</label></th>

        <td>
          <ul>
            <li class="player-box">
              <img src="<?php echo $avatar_url; ?>">
              <span class="player-name">
                <?php echo $user_data->name; ?>
              </span>
              <span class="player-title">
                <?php echo $user_data->title; ?>
              </span>
              <span class="player-world">
                <?php echo $user_data->world; ?>
              </span>
              <span class="player-fc">
                <?php echo $user_data->free_company; ?>
              </span>
              <div class="palyer-classes">
                <?php
                  foreach($user_data->classes as $class => $level) {
                    ?>
                      <span class="job-class">
                        <span><?php echo $class; ?></span>
                        <span><?php echo $level; ?></span>
                      </span>
                    <?php
                  } 
                ?>
              </div>
            </li>
          </ul>
        </td>
      </tr>

    </table>

    <?php
  }
}