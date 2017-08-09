<?php
private function transtext()
{

	// ------------------------------------------------------------ Table comments
	echo T_("comments");            // Table comments
	echo T_("comment");             // comment
	echo T_("approved");            // Enum approved
	echo T_("unapproved");          // Enum unapproved
	echo T_("spam");                // Enum spam
	echo T_("deleted");             // Enum deleted
	echo T_("id");                  // id
	echo T_("post");                // post_id
	echo T_("author");              // comment_author
	echo T_("email");               // comment_email
	echo T_("url");                 // comment_url
	echo T_("content");             // comment_content
	echo T_("meta");                // comment_meta
	echo T_("status");              // comment_status
	echo T_("parent");              // comment_parent
	echo T_("user");                // user_id
	echo T_("visitor");             // visitor_id
	echo T_("modified");            // date_modified

	// ------------------------------------------------------------ Table logitems
	echo T_("logitems");            // Table logitems
	echo T_("logitem");             // logitem
	echo T_("critical");            // Enum critical
	echo T_("high");                // Enum high
	echo T_("medium");              // Enum medium
	echo T_("low");                 // Enum low
	echo T_("title");               // logitem_title
	echo T_("desc");                // logitem_desc
	echo T_("meta");                // logitem_meta
	echo T_("priority");            // logitem_priority

	// ------------------------------------------------------------ Table logs
	echo T_("logs");                // Table logs
	echo T_("log");                 // log
	echo T_("enable");              // Enum enable
	echo T_("disable");             // Enum disable
	echo T_("expire");              // Enum expire
	echo T_("deliver");             // Enum deliver
	echo T_("logitem");             // logitem_id
	echo T_("data");                // log_data
	echo T_("meta");                // log_meta
	echo T_("status");              // log_status
	echo T_("createdate");          // log_createdate

	// ------------------------------------------------------------ Table notifications
	echo T_("notifications");       // Table notifications
	echo T_("notification");        // notification
	echo T_("read");                // Enum read
	echo T_("unread");              // Enum unread
	echo T_("user sender");         // user_idsender
	echo T_("title");               // notification_title
	echo T_("content");             // notification_content
	echo T_("meta");                // notification_meta
	echo T_("url");                 // notification_url
	echo T_("status");              // notification_status

	// ------------------------------------------------------------ Table options
	echo T_("options");             // Table options
	echo T_("option");              // option
	echo T_("cat");                 // option_cat
	echo T_("key");                 // option_key
	echo T_("value");               // option_value
	echo T_("meta");                // option_meta
	echo T_("status");              // option_status

	// ------------------------------------------------------------ Table posts
	echo T_("posts");               // Table posts
	echo T_("post");                // post
	echo T_("open");                // Enum open
	echo T_("closed");              // Enum closed
	echo T_("publish");             // Enum publish
	echo T_("draft");               // Enum draft
	echo T_("schedule");            // Enum schedule
	echo T_("language");            // post_language
	echo T_("title");               // post_title
	echo T_("slug");                // post_slug
	echo T_("url");                 // post_url
	echo T_("content");             // post_content
	echo T_("meta");                // post_meta
	echo T_("type");                // post_type
	echo T_("comment");             // post_comment
	echo T_("count");               // post_count
	echo T_("order");               // post_order
	echo T_("status");              // post_status
	echo T_("parent");              // post_parent
	echo T_("publishdate");         // post_publishdate

	// ------------------------------------------------------------ Table terms
	echo T_("terms");               // Table terms
	echo T_("term");                // term
	echo T_("language");            // term_language
	echo T_("type");                // term_type
	echo T_("title");               // term_title
	echo T_("slug");                // term_slug
	echo T_("url");                 // term_url
	echo T_("desc");                // term_desc
	echo T_("meta");                // term_meta
	echo T_("parent");              // term_parent

	// ------------------------------------------------------------ Table termusages
	echo T_("termusages");          // Table termusages
	echo T_("termusage");           // termusage
	echo T_("posts");               // Enum posts
	echo T_("products");            // Enum products
	echo T_("attachments");         // Enum attachments
	echo T_("files");               // Enum files
	echo T_("comments");            // Enum comments
	echo T_("term");                // term_id
	echo T_("termusage");           // termusage_id
	echo T_("foreign");             // termusage_foreign
	echo T_("order");               // termusage_order

	// ------------------------------------------------------------ Table users
	echo T_("users");               // Table users
	echo T_("user");                // user
	echo T_("active");              // Enum active
	echo T_("awaiting");            // Enum awaiting
	echo T_("deactive");            // Enum deactive
	echo T_("removed");             // Enum removed
	echo T_("filter");              // Enum filter
	echo T_("mobile");              // user_mobile
	echo T_("email");               // user_email
	echo T_("pass");                // user_pass
	echo T_("displayname");         // user_displayname
	echo T_("meta");                // user_meta
	echo T_("status");              // user_status
	echo T_("permission");          // user_permission
	echo T_("createdate");          // user_createdate

	// ------------------------------------------------------------ Table visitors
	echo T_("visitors");            // Table visitors
	echo T_("visitor");             // visitor
	echo T_("yes");                 // Enum yes
	echo T_("no");                  // Enum no
	echo T_("ip");                  // visitor_ip
	echo T_("url");                 // visitor_url
	echo T_("agent");               // visitor_agent
	echo T_("referer");             // visitor_referer
	echo T_("robot");               // visitor_robot
	echo T_("createdate");          // visitor_createdate

}
?>