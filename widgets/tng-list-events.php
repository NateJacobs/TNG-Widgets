<?php

class TNG_List_Events extends WP_Widget {
	public function __construct() {
		parent::__construct(
	 		'tng_List_events', // Base ID
			'TNG List Events', // Name
			array( 'description' => __( 'Displays a list of events from dates ranging from current date.', 'tng_domain' ), ) // Args
		);
	}

	public function widget( $args, $instance ) {
		// outputs the content of the widget
		global $languages_path, $text, $cms;
		
		//		if ( !mbtng_display_widget() ) {
					extract( $args );	
					// TNG general calls
					$tng_folder = get_option( 'mbtng_path' );
					chdir( $tng_folder );
					include( 'begin.php' );
					include_once( $cms['tngpath'] . "genlib.php" );
					include( $cms['tngpath'] . "getlang.php" );
					include( $cms['tngpath'] . "$mylanguage/text.php" );
					// TNG widget specfic calls
					$base_url = mbtng_base_url();

					/* User-selected settings. */
					$title = apply_filters( 'widget_title', $instance['title'] );
					
					if ($mylanguage == "languages/English" || $mylanguage == "languages/English-UTF8") {
						$text['events'] = "Events";
						$text['births'] = "Births";
						$text['anniversaries'] = "Anniversaries";
						$text['deaths'] = "Deaths";
						/* Set event type */
						if ( $instance['showBMD'] == "1" ) {
							$eventtype = $text['anniversaries'];
						} elseif ( $instance['showBMD'] == "2" ) {
							$eventtype = $text['deaths'];
					 	} else {
							$eventtype = $text['births'];
						}
						/* Widget titles in titlecase */
						$text['eventday'] = "day";
						$text['eventnonetoday'] = "No ". $eventtype ." Today";
						$text['eventnoneinrange'] = "No ". $eventtype ." for";
						$text['eventday'] = "Day";
						$text['eventdays'] = "Days";
						$text['eventheader'] = $eventtype ." for ";
						$text['eventsfrom'] = "from ";
 						$text['yearsago'] = " years ago";
					}
					/* Set dates to local time or server time (default)*/
					if ( $instance['time'] == "1" ) {
						$dateheading = displaydate (get_local_date('j M Y')); // get_local_date in plugin helpers.php
					} else {
						$dateheading = displaydate (date('j M Y'));
					}
					/* Build widget title */
					if ( $instance['range'] == "0" ) {
						$heading = $text['eventheader'] . $dateheading;
						$notfound = $text['eventnonetoday'];
					} else {
						$heading = $text['eventheader'] . $instance['range'] . " " . $text['eventdays'] . "<br />" . $text['eventsfrom'] . $dateheading;
						$notfound = $text['eventnoneinrange'] . " " . $instance['range'] . " " . $text['eventdays'];
					}
					
					/* Get Content */
					$content = tng_get_events_list( $base_url, $instance ); // or use rowcount as in mod
					if( !$content ) {
						$title = $notfound;
					} else {
						$title = $heading;
					}

					/* Before widget (defined by themes). */
					echo $before_widget;

					/* Title of widget (before and after defined by themes). */
					if ( ! empty( $title ) )
						echo $before_title . $title . $after_title;

					/* Display widget content from widget settings. */
					if ( $content ) {
						echo $content;
					}

					/* After widget (defined by themes). */
					echo $after_widget;
		//		}
	}

	public function update( $new_instance, $old_instance ) {

		// processes widget options to be saved
		$instance = $old_instance;
		/* Strip tags (if needed) and update the widget settings. */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['tree'] = strip_tags( $new_instance['tree'] );
		$instance['trees'] = $new_instance['trees'];
		$instance['showBMD'] = $new_instance['showBMD'];
		$instance['range'] = $new_instance['range'];
		$instance['showDate'] = $new_instance['showDate'];
		$instance['showLink'] = ( isset( $new_instance['showLink'] ) ? 1 : 0 );
		$instance['showAgo'] = ( isset( $new_instance['showAgo'] ) ? 1 : 0 );
		$instance['time'] = ( isset( $new_instance['time'] ) ? 1 : 0 );
		return $instance;
	}

 	public function form( $instance ) {

		// outputs the options form on admin
		if ( empty( $instance[ 'title' ] ) ) {
			$instance['title'] = __( 'Births', 'text_domain' );
		}
		if ( empty( $instance[ 'tree' ] ) ) {
			$instance['tree'] = "tree1";
		}
		if ( empty( $instance[ 'range' ] ) ) {
			$instance['range'] = "0";
		}
		$title = $instance[ 'title' ]; // widget title (text)
		$tree = $instance['tree']; // TNG tree (text)

		$showBMD = $instance['showBMD']; // 0 = births; 1 = anniversaries; 2 = deaths (droplist)
		$range = $instance['range']; // 0 = today only; # = range of days from today (number)
		$showDate = $instance['showDate']; // show add date or year after link (droplist)
		$showLink = $instance['showLink']; // full name links to TNG person (checkbox)
		$showAgo = $instance['showAgo']; // show "years ago" after date (checkbox)
		$time = $instance['time']; // 0 = server time; 1 = local time (options)

/*
// TODO: TREE SELECTION DROPLIST
		<p class="tng-widget-option-droplist">
			<label for="<?php echo $this->get_field_id( 'trees' ); ?>" class="tng-widget-label tng-trees"><?php _e( 'Select TNG Tree.' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'trees' ); ?>" name="<?php echo $this->get_field_name( 'trees' ); ?>" class="widefat">
			<?php
			$trees = tng_get_trees($instance);
			foreach( $trees as $key => $value ) {
			?>
				<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $instance['tree'] ); ?>><?php echo $value; ?></option>
			<?php
			 } ?>
			</select>
		</p>
*/
		?>
		<p class="tng-widget-admin tng-option-title">
			<label for="<?php echo $this->get_field_id( 'title' ); ?>" class="tng-widget-label tng-title"><?php _e( 'Title:' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" class="tng-widget-input tng-title" />
		</p>
		<p class="tng-widget-admin tng-option-text">
			<label for="<?php echo $this->get_field_id( 'tree' ); ?>" class="tng-widget-label tng-title"><?php _e( 'TNG Tree code:' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'tree' ); ?>" name="<?php echo $this->get_field_name( 'tree' ); ?>" type="text" value="<?php echo esc_attr( $tree ); ?>" class="tng-widget-input tng-tree" />
		</p>
		<p class="tng-widget-admin tng-option-text">
			<label for="<?php echo $this->get_field_id( 'range' ); ?>" class="tng-widget-label tng-range"><?php _e( 'Range of Days:' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'range' ); ?>" name="<?php echo $this->get_field_name( 'range' ); ?>" type="text" value="<?php echo esc_attr( $range ); ?>" class="tng-widget-input tng-range" />
		</p>
		<p class="tng-widget-admin tng-option-droplist">
			<label for="<?php echo $this->get_field_id( 'showBMD' ); ?>" class="tng-widget-label tng-showdate"><?php _e( 'Show Event:' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'showBMD' ); ?>" name="<?php echo $this->get_field_name( 'showBMD' ); ?>" class="widefat">
				<option value="0" <?php if ( '0' == $instance['showBMD'] ) echo 'selected="selected"'; ?>>Births</option>
				<option value="1" <?php if ( '1' == $instance['showBMD'] ) echo 'selected="selected"'; ?>>Anniversaries</option>
				<option value="2" <?php if ( '2' == $instance['showBMD'] ) echo 'selected="selected"'; ?>>Deaths</option>
<?php /* until CUSTOM EVENT is built
			foreach ($customevent) {
				<option value="1" <?php if ( '1' == $instance['showBMD'] ) echo 'selected="selected"'; ?>>Anniversaries</option>
			}
*/?>
			</select>
		</p>
		<p class="tng-widget-admin tng-option-droplist">
			<label for="<?php echo $this->get_field_id( 'showDate' ); ?>" class="tng-widget-label tng-showdate"><?php _e( 'Date Format:' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'showDate' ); ?>" name="<?php echo $this->get_field_name( 'showDate' ); ?>" class="widefat">
				<option value="0" <?php if ( '0' == $instance['showDate'] ) echo 'selected="selected"'; ?>>No Date</option>
				<option value="1" <?php if ( '1' == $instance['showDate'] ) echo 'selected="selected"'; ?>>Full Date</option>
				<option value="2" <?php if ( '2' == $instance['showDate'] ) echo 'selected="selected"'; ?>>Year Only</option>
			</select>
		</p>
		<p class="tng-widget-admin tng-option-checkbox">
			<input id="<?php echo $this->get_field_id( 'showLink' ); ?>" name="<?php echo $this->get_field_name( 'showLink' ); ?>" <?php checked( $instance['showLink'], true ); ?> type="checkbox" class="checkbox tng-widget-input tng-showlink" /> 
			<label for="<?php echo $this->get_field_id( 'showLink' ); ?>" class="tng-widget-label tng-showlink"><?php _e( 'Link to TNG person.' ); ?></label>
		</p>
		<p class="tng-widget-admin tng-option-checkbox">
			<input id="<?php echo $this->get_field_id( 'showAgo' ); ?>" name="<?php echo $this->get_field_name( 'showAgo' ); ?>" <?php checked( $instance['showAgo'], true ); ?> type="checkbox" class="checkbox tng-widget-input tng-showago" /> 
			<label for="<?php echo $this->get_field_id( 'showAgo' ); ?>" class="tng-widget-label tng-showago"><?php _e( 'Show how many years ago.' ); ?></label>
		</p>
		<p class="tng-widget-admin tng-option-checkbox">
			<input id="<?php echo $this->get_field_id( 'time' ); ?>" name="<?php echo $this->get_field_name( 'time' ); ?>" <?php checked( $instance['time'], true ); ?> type="checkbox" class="checkbox tng-widget-input tng-time" /> 
			<label for="<?php echo $this->get_field_id( 'time' ); ?>" class="tng-widget-label tng-time"><?php _e( 'Calculate using local time.' ); ?></label>
		</p>
		
		<?php
//		ADD CHECKBOX LINK TO INDIVIDUAL TABLE OR ANCESTOR CHART
	}
}

function tng_get_events_list( $base_url, $instance ) {
	global $text;
// TODO: Convert to HTML5 Canvas compatible jQuery Cloud eg. http://www.goat1000.com/tagcanvas.php
// ASK: What advantage using TNGs getURL function?
	$events = tng_get_events_data( $instance );
	if( $events ) {
		$list_html = '<span class="tng-widget-content">';
		$list_html .= '<ul class="tng-list no-bullets">';
		foreach ($events as $event) {
			if ( $instance['showBMD'] == "1" ) { // FAMILY
				$linktype = "familygroup";
				$linkid = "familyID"; // $dbrow['familyID'] $dbrow['gedcom'] 
				$names = tng_get_family_data($event);
			} else { // PERSON
				$linktype = "getperson";
				$linkid = "personID";
				$lastname = $event['lastname']; // $event[0]
				$firstname = $event['firstname']; // $event[1]
				$lnprefix = $event['lnprefix']; // $event[8]
// ASK: Need to use urlencode?
				$names = $firstname . ' ' . $lnprefix . ' ' . $lastname;
			}
			$year = displaydate ( $event[3]); // BirthYear, MarriageYear, DeathYear (NOTE: EventYear - needs to be generated)
			$date = displaydate ( $event[4]); // birthdate, marrdate, deathdate or eventdate
			$ID = $event[6]; // familyID or personID
			$tree = $event['gedcom']; // $event[5]
			$link = $cms['tngpath'] . $linktype . ".php?" . $linkid . "=" . $ID . "&amp;tree=" . $tree;
			if ( $instance['showAgo'] ) {
				$ago = date('Y')-substr( $year, 0, 4 );
				$agostring = " (" . $ago . " " . $text['yearsago'] . ")";
			}
			$list_html .= "<li>\n";
			if ( $instance['showLink'] == "1" ) $list_html .= '<a href=' . $link . ">\n";
			$list_html .= $names;
			if ( $instance['showLink'] == "1" ) $list_html .= "</a>\n";
			if ( $instance['showDate'] == "1" ) $list_html .= ' <span class="tng-list-date">' . $date . '</span>';
			if ( $instance['showDate'] == "2" ) $list_html .= ' <span class="tng-list-date">' . $year . '</span>';
			if ( $instance['showAgo'] ) $list_html .= ' <span class="tng-list-ago">' . $agostring . '</span>';
			$list_html .= "</li>\n";
		}
		$list_html .= "</ul>\n</span>\n";
		return $list_html;
	}
}

function tng_get_events_data( $instance ) {
	global $families_table, $people_table;
// ASK: IS USER CHECK REQUIRED?
//	if( $currentuser && $allow_living ) {}
	$link = mbtng_db_connect() or exit;
	$wherestr = "WHERE lastname != \"\" and lastname != \"[--?--]\""; // suppress "[no surname]" and [--?--]
	$displaydays = $instance['range'] + 1;
	$agostring = "";
	if ( $instance['time'] == "1" ) {
		$datetouse = get_local_date('Y-m-d');
	} else {
		$datetouse = date("Y-m-d");
	}
	if ( $instance['showBMD'] == "1" ) {		// ANNIVERSARIES
		$BD = "";
		$BMD = array( "husband", "wife", "marrdatetr", "MarriageYear", "marrdate", "familyID", "nextmarriage");
		$BMD_table = $families_table;
	} else {
		$BD = ", lnprefix, living"; // Person only keys
		if ( $instance['showBMD'] == "2" ) {	// DEATHS
			$BMD = array( "lastname", "firstname", "deathdatetr", "DeathYear", "deathdate", "personID", "nextdeathday");
			$BMD_table = $people_table;
	 	} else {								// BIRTHS = DEFAULT
			$BMD = array( "lastname", "firstname", "birthdatetr", "BirthYear", "birthdate", "personID", "nextbirthday");
			$BMD_table = $people_table;
		}
	}
	$query = "SELECT $BMD[0], $BMD[1], $BMD[2], YEAR($BMD[2]) AS $BMD[3], $BMD[4], gedcom, $BMD[5], ";
	$query .= "$BMD[2] + INTERVAL YEAR('" . $datetouse . "') - YEAR( $BMD[2] ) + ( ($BMD[2] + INTERVAL YEAR('" . $datetouse . "') - YEAR( $BMD[2] ) YEAR) < '" . $datetouse . "') YEAR as $BMD[6]";
	$query .= "$BD"; // lnprefix and personID if Birth or Death - at end to make anniversaries and HTML output easier
	$query .= " FROM $BMD_table";
	$query .= " WHERE DATEDIFF( $BMD[2] + INTERVAL YEAR('" . $datetouse . "') - YEAR( $BMD[2] ) + ( ( $BMD[2] + INTERVAL YEAR('" . $datetouse . "') - YEAR($BMD[2]) YEAR) < '" . $datetouse . "') YEAR, '" . $datetouse . "') <= " . $instance['range'];
	if( !$currentuser && !$allow_living ) $query .= " AND Living = 0";
	$query .= " ORDER BY $BMD[6], $BMD[2]";
	$result = mysql_query($query) or die ("$text[cannotexecutequery]: $query");
	$arr = array();
// ASK: given die above, is this 'if' redundant?
	if( $result ) {
		while( $row = mysql_fetch_array($result) ) {
			$arr[] = $row;
		}
		mysql_free_result($result);
	}
	return $arr;
}

function tng_get_family_data($event){ // TNG: already exists similar function???
	global $people_table, $text;
// ASK: IS USER CHECK REQUIRED?
//	if( $currentuser && $allow_living ) {}
	$link = mbtng_db_connect() or exit;
	$text['spouse'] = "spouse"; // ASK: Singular versions could be included in TNG...
	$spouse1ID = $event['husband'];
	$spouse2ID = $event['wife'];
	$tree = $event['gedcom'];
	$names_html = '';
	if( $spouse1ID || $spouse2ID ) {
		if( $spouse1ID ) {
			$result = getPersonData($tree, $spouse1ID); // TNG function: getPersonData($tree, $personID)
			$row = mysql_fetch_assoc($result);
			$spouse1 = getSurnameOnly($row); // TNG function: getNameUniversal($row, $sortorder [firstlast | lastfirst | lastfirstsuffix])
//
		} else { $spouse1 = $text['spouse']; }
		if( $spouse2ID ) {
			$result = getPersonData($tree, $spouse2ID); // TNG function: getPersonData($tree, $personID)
			$row = mysql_fetch_assoc($result);
			$spouse2 = getSurnameOnly($row); // TNG function: 
		} else { $spouse2 = $text['spouse']; }
		$names_html .= $spouse1 . ' &amp; ' . $spouse2;		
	} else {
		$names_html .= $text['spouses'] . ' (' . $text['unknown'] . ')';
	}
	return $names_html;
}

function tng_get_trees($instance) {
/*	global $trees_table;
	$link = mbtng_db_connect() or exit;
	$query = "SELECT gedcom, treename FROM $trees_table ORDER BY treename";
	$treeresult = mysql_query($query) or die ($admtext['cannotexecutequery'] . ": $query");
	$arr = array();
	while( $row = mysql_fetch_array($treeresult) ) {
//		$arr[$row['gedcom']] = $row['treename'];
		$arr[] = $row['treename'];
	}
	mysql_free_result($treeresult);

*/	$arr = array("tree1" => "Tree 1", "tree2" => "Tree 2",);
	return $arr;	
}
