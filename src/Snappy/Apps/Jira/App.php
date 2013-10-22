<?php namespace Snappy\Apps\Jira;

use Snappy\Apps\App as BaseApp;
use Snappy\Apps\TagsChangedHandler;

class App extends BaseApp implements TagsChangedHandler {

	/**
	 * The name of the application.
	 *
	 * @var string
	 */
	public $name = 'Jira';

	/**
	 * The application description.
	 *
	 * @var string
	 */
	public $description = 'The Jira integration allows you to convert tickets into Jira issues.';

	/**
	 * Any notes about this application
	 *
	 * @var string
	 */
	public $notes = '';

	/**
	 * The application's icon filename.
	 *
	 * @var string
	 */
	public $icon = 'jira.png';

	/**
	 * The application service's main website.
	 *
	 * @var string
	 */
	public $website = 'https://www.atlassian.com/software/jira';

	/**
	 * The application author name.
	 *
	 * @var string
	 */
	public $author = 'UserScape, Inc.';

	/**
	 * The application author e-mail.
	 *
	 * @var string
	 */
	public $email = 'it@userscape.com';

	/**
	 * The settings required by the application.
	 *
	 * @var array
	 */
	public $settings = array(
		array('name' => 'username', 'type' => 'text', 'help' => 'Your Jira username', 'validate' => 'required'),
		array('name' => 'password', 'type' => 'password', 'help' => 'Your Jira password', 'validate' => 'required'),
		array('name' => 'url', 'type' => 'text', 'help' => 'Your Jira root URL', 'validate' => 'required'),
		array('name' => 'project', 'type' => 'text', 'help' => 'Your Jira project key', 'validate' => 'required'),
		array('name' => 'type', 'type' => 'text', 'help' => 'The issue type to assign to newly created issues', 'value' => 'New Feature', 'validate' => 'required'),
		array('name' => 'tag', 'label' => 'Watch for tag', 'type' => 'text', 'placeholder' => '#jira', 'help' => 'Tickets with this tag will create an issue in Jira.', 'validate' => 'required'),
	);

	/**
	 * Handle tags changed.
	 *
	 * @param  array  $ticket
	 * @param  array  $added
	 * @param  array  $removed
	 * @return void
	 */
	public function handleTagsChanged(array $ticket, array $added, array $removed)
	{
		if (in_array($this->config['tag'], $added))
		{
			$link = 'URL: https://app.besnappy.com/#ticket/'.$ticket['id'];
			$body = head($ticket['notes']);
			$body = $link.PHP_EOL.PHP_EOL.$body['content'];

			$payload = array(
				'fields' => array(
					'project' => array('key' => $this->config['project']),
					'summary' => $ticket['default_subject'],
					'description' => $body,
					'issuetype' => array('name' => $this->config['type']),
				),
			);

			$client = new \Guzzle\Http\Client;
			$client->setSslVerification(false);

			$request = $client->post(
				trim($this->config['url'], '/').'/rest/api/latest/issue',
				array('Content-Type' => 'application/json'),
				json_encode($payload)
			);

			$request->setAuth($this->config['username'], $this->config['password']);

			try
			{
				$response = $request->send();
			}
			catch (\Exception $e)
			{
				\Log::exception($e);
			}
		}
	}

}
