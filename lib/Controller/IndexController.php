<?php
namespace OCA\Joplin\Controller;

use OCP\IRequest;
use OCP\ISession;
use OCP\AppFramework\Http\TemplateResponse;
use OCA\Joplin\Error\NotFoundException;
use OCA\Joplin\Service\JoplinService;
use OCA\Joplin\Service\ModelService;
use OCP\AppFramework\Controller;
use OCA\Joplin\Error\ErrorHandler;

class IndexController extends Controller {

	private $joplinService_;
	private $models_;
	private $userId_;

	public function __construct($AppName, $UserId, IRequest $request, ISession $session, ModelService $ModelService, JoplinService $JoplinService){
		parent::__construct($AppName, $request, $session);
		$this->joplinService_ = $JoplinService;
		$this->models_ = $ModelService;
		$this->userId_ = $UserId;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function get() {
		try {
			$syncTargets = $this->models_->get('syncTarget')->fetchAllByUserId($this->userId_);
			$shares = $this->models_->get('share')->fetchAllByUserId($this->userId_);
			
			$syncTargetsHtml = $this->joplinService_->renderDbTable([
				'uuid' => ['label' => 'ID'],
				'path' => ['label' => 'Path'],
			], $syncTargets);

			$sharesHtml = $this->joplinService_->renderDbTable([
				'uuid' => ['label' => 'ID'],
				'item_id' => ['label' => 'Note ID'],
			], $shares);

			return $this->joplinService_->renderTemplate('content/index', [
				'syncTargetsHtml' => $syncTargetsHtml,
				'sharesHtml' => $sharesHtml,
			]);
		} catch (\Exception $e) {
			return ErrorHandler::toHtmlResponse($e);
		}
	}

}
