<?php if (!defined('APPLICATION')) exit();

// Conversations
$Configuration['Conversations']['Version'] = '2.0.18.4';

// Database
$Configuration['Database']['Name'] = 'pp-forum';
$Configuration['Database']['Host'] = 'localhost';
$Configuration['Database']['User'] = 'root';
$Configuration['Database']['Password'] = '';

// EnabledApplications
$Configuration['EnabledApplications']['Vanilla'] = 'vanilla';
$Configuration['EnabledApplications']['Conversations'] = 'conversations';

// EnabledPlugins
$Configuration['EnabledPlugins']['GettingStarted'] = 'GettingStarted';
$Configuration['EnabledPlugins']['HtmLawed'] = 'HtmLawed';
$Configuration['EnabledPlugins']['Flagging'] = TRUE;
$Configuration['EnabledPlugins']['Gravatar'] = TRUE;
$Configuration['EnabledPlugins']['SplitMerge'] = TRUE;
$Configuration['EnabledPlugins']['Tagging'] = TRUE;
$Configuration['EnabledPlugins']['ProxyConnect'] = TRUE;
$Configuration['EnabledPlugins']['ProxyConnectManual'] = TRUE;
$Configuration['EnabledPlugins']['VanillaInThisDiscussion'] = TRUE;
$Configuration['EnabledPlugins']['AuthorTimeView'] = TRUE;
$Configuration['EnabledPlugins']['CategoryButtons'] = TRUE;
$Configuration['EnabledPlugins']['Kudos'] = TRUE;
$Configuration['EnabledPlugins']['MoreMessagesPositions'] = TRUE;
$Configuration['EnabledPlugins']['MyProfile'] = TRUE;
$Configuration['EnabledPlugins']['PageNavigator'] = TRUE;
$Configuration['EnabledPlugins']['Participated'] = TRUE;
$Configuration['EnabledPlugins']['PostCount'] = TRUE;
$Configuration['EnabledPlugins']['QuoteSelection'] = TRUE;
$Configuration['EnabledPlugins']['RoleBadges'] = TRUE;
$Configuration['EnabledPlugins']['RecentActivity'] = TRUE;
$Configuration['EnabledPlugins']['UnreadIcon'] = TRUE;

// Garden
$Configuration['Garden']['Title'] = 'Community';
$Configuration['Garden']['Cookie']['Salt'] = 'NV0ZX7BIJ5';
$Configuration['Garden']['Cookie']['Domain'] = '';
$Configuration['Garden']['Registration']['ConfirmEmail'] = TRUE;
$Configuration['Garden']['Email']['SupportName'] = 'Practical Plants';
$Configuration['Garden']['Email']['SupportAddress'] = 'forum@practicalplants.org';
$Configuration['Garden']['Email']['UseSmtp'] = FALSE;
$Configuration['Garden']['Email']['SmtpHost'] = '';
$Configuration['Garden']['Email']['SmtpUser'] = '';
$Configuration['Garden']['Email']['SmtpPassword'] = '';
$Configuration['Garden']['Email']['SmtpPort'] = '25';
$Configuration['Garden']['Email']['SmtpSecurity'] = '';
$Configuration['Garden']['Version'] = '2.0.18.4';
$Configuration['Garden']['RewriteUrls'] = TRUE;
$Configuration['Garden']['CanProcessImages'] = TRUE;
$Configuration['Garden']['Installed'] = TRUE;
$Configuration['Garden']['Theme'] = 'practicalplants';
$Configuration['Garden']['Messages']['Cache'] = 'a:1:{i:0;s:6:"[Base]";}';
$Configuration['Garden']['PluginManager']['Search'] = 'a:1:{s:87:"/Users/andru/Development/practicalplants/public/community/plugins/ProxyConnect/internal";s:17:"ProxyConnect RIMs";}';
$Configuration['Garden']['Authenticators']['proxy']['Name'] = 'ProxyConnect';
$Configuration['Garden']['Authenticators']['proxy']['CookieName'] = 'VanillaProxy';
$Configuration['Garden']['Authenticator']['EnabledSchemes'] = 'a:2:{i:0;s:8:"password";i:1;s:5:"proxy";}';
$Configuration['Garden']['Authenticator']['DefaultScheme'] = 'proxy';
$Configuration['Garden']['SignIn']['Popup'] = FALSE;
$Configuration['Garden']['Analytics']['AllowLocal'] = TRUE;
$Configuration['Garden']['InstallationID'] = 'D2ED-3489B202-08100746';
$Configuration['Garden']['InstallationSecret'] = 'dad20a28e22b5e9ea6ba94b1ad3a2fdb4b0bc52b';
$Configuration['Garden']['EditContentTimeout'] = '350';

// Plugin
$Configuration['Plugin']['ProxyConnect']['IntegrationManager'] = 'proxyconnectmanual';

// Plugins
$Configuration['Plugins']['GettingStarted']['Dashboard'] = '1';
$Configuration['Plugins']['GettingStarted']['Categories'] = '1';
$Configuration['Plugins']['GettingStarted']['Plugins'] = '1';
$Configuration['Plugins']['GettingStarted']['Discussion'] = '1';
$Configuration['Plugins']['GettingStarted']['Profile'] = '1';
$Configuration['Plugins']['Tagging']['Enabled'] = TRUE;
$Configuration['Plugins']['OpenID']['Enabled'] = TRUE;
$Configuration['Plugins']['ProxyConnect']['Enabled'] = TRUE;
$Configuration['Plugins']['AuthorTimeView']['Show_AuthorTime'] = FALSE;
$Configuration['Plugins']['AuthorTimeView']['Show_Vcount'] = '1';
$Configuration['Plugins']['MyProfile']['Version'] = '0.1.9b';

// Routes
$Configuration['Routes']['DefaultController'] = 'a:2:{i:0;s:11:"discussions";i:1;s:8:"Internal";}';

// Vanilla
$Configuration['Vanilla']['Version'] = '2.0.18.4';
$Configuration['Vanilla']['Discussion']['SpamCount'] = '3';
$Configuration['Vanilla']['Discussion']['SpamTime'] = '60';
$Configuration['Vanilla']['Discussion']['SpamLock'] = '600';
$Configuration['Vanilla']['Comment']['SpamCount'] = '5';
$Configuration['Vanilla']['Comment']['SpamTime'] = '60';
$Configuration['Vanilla']['Comment']['SpamLock'] = '120';
$Configuration['Vanilla']['Comment']['MaxLength'] = '8000';
$Configuration['Vanilla']['AdminCheckboxes']['Use'] = TRUE;
$Configuration['Vanilla']['Categories']['MaxDisplayDepth'] = '4';
$Configuration['Vanilla']['Categories']['DoHeadings'] = '1';
$Configuration['Vanilla']['Categories']['HideModule'] = FALSE;
$Configuration['Vanilla']['Discussions']['PerPage'] = '30';
$Configuration['Vanilla']['Comments']['AutoRefresh'] = '0';
$Configuration['Vanilla']['Comments']['PerPage'] = '50';
$Configuration['Vanilla']['Archive']['Date'] = '';
$Configuration['Vanilla']['Archive']['Exclude'] = FALSE;

// Last edited by viator (10.0.2.9)2012-07-22 07:12:39