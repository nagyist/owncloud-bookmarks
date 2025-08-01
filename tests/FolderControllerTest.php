<?php

namespace OCA\Bookmarks\Tests;

use OC;
use OCA\Bookmarks\Controller\FoldersController;
use OCA\Bookmarks\Db\Bookmark;
use OCA\Bookmarks\Db\BookmarkMapper;
use OCA\Bookmarks\Db\Folder;
use OCA\Bookmarks\Db\FolderMapper;
use OCA\Bookmarks\Db\PublicFolder;
use OCA\Bookmarks\Db\PublicFolderMapper;
use OCA\Bookmarks\Db\Share;
use OCA\Bookmarks\Db\SharedFolder;
use OCA\Bookmarks\Db\SharedFolderMapper;
use OCA\Bookmarks\Db\ShareMapper;
use OCA\Bookmarks\Db\TagMapper;
use OCA\Bookmarks\Db\TreeMapper;
use OCA\Bookmarks\Exception\AlreadyExistsError;
use OCA\Bookmarks\Exception\UnauthenticatedError;
use OCA\Bookmarks\Exception\UrlParseError;
use OCA\Bookmarks\Exception\UserLimitExceededError;
use OCA\Bookmarks\Service\Authorizer;
use OCA\Bookmarks\Service\BookmarkService;
use OCA\Bookmarks\Service\FolderService;
use OCA\Bookmarks\Service\TreeCacheManager;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\QueryException;
use OCP\ICache;
use OCP\ICacheFactory;
use OCP\IGroupManager;
use OCP\IRequest;
use OCP\IUserManager;
use OCP\IUserSession;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Test_BookmarkController
 *
 * @group Controller
 */
class FolderControllerTest extends TestCase {
	private $userId;

	private $otherUser;
	private \OCP\IRequest $request;
	private \OC\User\Manager $userManager;
	private FoldersController $controller;
	private FoldersController $publicController;
	private BookmarkMapper $bookmarkMapper;
	private FolderMapper $folderMapper;
	private TagMapper $tagMapper;
	private PublicFolderMapper $publicFolderMapper;
	private IGroupManager $groupManager;

	private $bookmark1Id;
	private $bookmark2Id;

	private PublicFolder $publicFolder;

	private Folder $folder1;

	private Folder $folder2;

	private string $otherUserId;

	private ShareMapper $shareMapper;
	private SharedFolderMapper $sharedFolderMapper;

	private \OCP\IGroup $group;
	private TreeMapper $treeMapper;
	private FoldersController $otherController;
	private FoldersController $public;
	private FoldersController $noauth;
	private IRequest $publicRequest;
	private string $user;
	private Share $share;
	private SharedFolder $sharedFolder;
	private TreeCacheManager $hashManager;
	private Authorizer $authorizer;
	private FolderService $folders;
	private BookmarkService $bookmarks;


	/**
	 * @throws QueryException
	 */
	protected function setUp(): void {
		parent::setUp();
		$this->cleanUp();

		$this->user = 'test';
		$this->otherUser = 'otheruser';
		$this->request = OC::$server->get(IRequest::class);

		$this->publicRequest = $this->createMock(IRequest::class);

		$this->userManager = OC::$server->get(IUserManager::class);
		if (!$this->userManager->userExists($this->user)) {
			$this->userManager->createUser($this->user, 'password');
		}
		$this->userId = $this->userManager->get($this->user)->getUID();
		if (!$this->userManager->userExists($this->otherUser)) {
			$this->userManager->createUser($this->otherUser, 'password');
		}
		$this->otherUserId = $this->userManager->get($this->otherUser)->getUID();

		$this->bookmarkMapper = OC::$server->get(BookmarkMapper::class);
		$this->tagMapper = OC::$server->get(TagMapper::class);
		$this->folderMapper = OC::$server->get(FolderMapper::class);
		$this->treeMapper = OC::$server->get(TreeMapper::class);
		$this->publicFolderMapper = OC::$server->get(PublicFolderMapper::class);
		$this->shareMapper = OC::$server->get(ShareMapper::class);
		$this->sharedFolderMapper = OC::$server->get(SharedFolderMapper::class);
		$this->emptyCache = $emptyCache = $this->createMock(ICache::class);
		$emptyCache->method('get')->willReturn(null);
		$emptyCache->method('set')->willReturn(true);
		$emptyCache->method('hasKey')->willReturn(false);
		$emptyCache->method('remove')->willReturn(true);
		$emptyCache->method('clear')->willReturn(true);
		$this->cacheFactory = $this->createMock(ICacheFactory::class);
		$this->cacheFactory->method('createDistributed')->willReturn($this->emptyCache);
		$this->hashManager = new TreeCacheManager(
			$this->folderMapper,
			$this->bookmarkMapper,
			$this->shareMapper,
			$this->sharedFolderMapper,
			$this->cacheFactory,
			OC::$server->get(ContainerInterface::class),
			$this->tagMapper,
		);
		$this->folders = OC::$server->get(FolderService::class);
		$this->bookmarks = OC::$server->get(BookmarkService::class);
		$this->groupManager = OC::$server->get(IGroupManager::class);
		$loggerInterface = OC::$server->get(LoggerInterface::class);

		$this->group = $this->groupManager->createGroup('foobar');
		$this->group->addUser($this->userManager->get($this->otherUser));

		$userSession = $this->createMock(IUserSession::class);
		$userSession->method('isLoggedIn')->willReturn(false);
		$userSession->method('getUser')->willReturn(null);
		$this->authorizer = new Authorizer(
			$this->folderMapper,
			$this->bookmarkMapper,
			$this->publicFolderMapper,
			$this->shareMapper,
			$this->treeMapper,
			$userSession,
			$this->sharedFolderMapper,
		);

		$this->controller = new FoldersController('bookmarks', $this->request, $this->folderMapper, $this->publicFolderMapper, $this->shareMapper, $this->treeMapper, $this->authorizer, $this->hashManager, $this->folders, $this->bookmarks, $loggerInterface, $this->userManager);
		$this->otherController = new FoldersController('bookmarks', $this->request, $this->folderMapper, $this->publicFolderMapper, $this->shareMapper, $this->treeMapper, $this->authorizer, $this->hashManager, $this->folders, $this->bookmarks, $loggerInterface, $this->userManager);
		$this->public = new FoldersController('bookmarks', $this->publicRequest, $this->folderMapper, $this->publicFolderMapper, $this->shareMapper, $this->treeMapper, $this->authorizer, $this->hashManager, $this->folders, $this->bookmarks, $loggerInterface, $this->userManager);
		$this->noauth = new FoldersController('bookmarks', $this->request, $this->folderMapper, $this->publicFolderMapper, $this->shareMapper, $this->treeMapper, $this->authorizer, $this->hashManager, $this->folders, $this->bookmarks, $loggerInterface, $this->userManager);
	}

	/**
	 * @throws AlreadyExistsError
	 * @throws UrlParseError
	 * @throws UserLimitExceededError
	 * @throws MultipleObjectsReturnedException
	 */
	public function setupBookmarks() {
		$this->authorizer->setUserId($this->userId);
		$this->folder1 = new Folder();
		$this->folder1->setTitle('foo');
		$this->folder1->setUserId($this->userId);
		$this->folderMapper->insert($this->folder1);

		$this->folder2 = new Folder();
		$this->folder2->setTitle('bar');
		$this->folder2->setUserId($this->userId);
		$this->folderMapper->insert($this->folder2);

		$this->folder3 = new Folder();
		$this->folder3->setTitle('bar');
		$this->folder3->setUserId($this->userId);
		$this->folderMapper->insert($this->folder3);

		$this->folder4 = new Folder();
		$this->folder4->setTitle('bar');
		$this->folder4->setUserId($this->otherUserId);
		$this->folderMapper->insert($this->folder4);

		$this->treeMapper->move(TreeMapper::TYPE_FOLDER, $this->folder1->getId(), $this->folderMapper->findRootFolder($this->userId)->getId());
		$this->treeMapper->move(TreeMapper::TYPE_FOLDER, $this->folder2->getId(), $this->folder1->getId());
		$this->treeMapper->move(TreeMapper::TYPE_FOLDER, $this->folder3->getId(), $this->folderMapper->findRootFolder($this->userId)->getId());
		$this->treeMapper->move(TreeMapper::TYPE_FOLDER, $this->folder4->getId(), $this->folderMapper->findRootFolder($this->otherUserId)->getId());

		$bookmark1 = Bookmark::fromArray([
			'userId' => $this->userId,
			'url' => 'https://www.golem.de',
			'title' => 'Golem',
			'description' => 'PublicNoTag',
		]);
		$bookmark1 = $this->bookmarkMapper->insertOrUpdate($bookmark1);
		$this->tagMapper->addTo(['four'], $bookmark1->getId());
		$this->treeMapper->addToFolders(TreeMapper::TYPE_BOOKMARK, $bookmark1->getId(), [$this->folder1->getId()]);

		$bookmark2 = Bookmark::fromArray([
			'userId' => $this->userId,
			'url' => 'https://9gag.com',
			'title' => '9gag',
			'description' => 'PublicTag',
		]);
		$bookmark2 = $this->bookmarkMapper->insertOrUpdate($bookmark2);
		$this->tagMapper->addTo(['four'], $bookmark2->getId());
		$this->treeMapper->addToFolders(TreeMapper::TYPE_BOOKMARK, $bookmark2->getId(), [$this->folder2->getId()]);

		$this->bookmark1Id = $bookmark1->getId();
		$this->bookmark2Id = $bookmark2->getId();
	}

	/**
	 * @throws MultipleObjectsReturnedException
	 */
	public function setupPublicFolder(): void {
		$this->authorizer->setUserId($this->userId);
		$this->publicFolder = new PublicFolder();
		$this->publicFolder->setDescription('');
		$this->publicFolder->setFolderId($this->folder1->getId());
		$this->publicFolderMapper->insert($this->publicFolder);

		// inject token into public request stub
		$this->publicRequest->method('getHeader')
			->willReturn('Bearer ' . $this->publicFolder->getId());
	}

	/**
	 * @throws MultipleObjectsReturnedException
	 * @throws \OCA\Bookmarks\Exception\UnsupportedOperation
	 * @throws \OCP\AppFramework\Db\DoesNotExistException
	 */
	public function setupSharedFolder($canWrite = true, $canShare = false) {
		$this->authorizer->setUserId($this->userId);
		$this->share = $this->folders->createShare($this->folder1->getId(), $this->otherUser, \OCP\Share\IShare::TYPE_USER, $canWrite, $canShare);
	}

	/**
	 * @throws AlreadyExistsError
	 * @throws UrlParseError
	 * @throws UserLimitExceededError
	 * @throws \OCP\AppFramework\Db\DoesNotExistException
	 * @throws MultipleObjectsReturnedException
	 */
	public function testRead(): void {

		$this->setupBookmarks();
		$this->authorizer->setUserId($this->userId);
		$output = $this->controller->getFolder($this->folder1->getId());
		$data = $output->getData();
		$this->assertEquals('success', $data['status'], var_export($data, true));
		$this->assertEquals($this->folder1->getTitle(), $data['item']['title']);
	}

	/**
	 * @throws AlreadyExistsError
	 * @throws UrlParseError
	 * @throws UserLimitExceededError
	 * @throws \OCP\AppFramework\Db\DoesNotExistException
	 * @throws MultipleObjectsReturnedException
	 * @dataProvider hashCacheDataProvider
	 */
	public function testHash(bool $useCache): void {
		if ($useCache) {
			$this->cacheFactory = $this->createMock(ICacheFactory::class);
			$this->cacheFactory->method('createDistributed')
				->willReturnCallback(fn ()
				  => OC::$server->get(ICacheFactory::class)
				  	->createDistributed(time() . '' . random_int(0, 1000000))
				);
		}

		$this->setupBookmarks();
		$this->authorizer->setUserId($this->userId);

		// get hash for title,url
		$output = $this->controller->hashFolder(-1);
		$data1 = $output->getData();
		$this->assertEquals('success', $data1['status'], var_export($data1, true));

		// get hash for title,url,tags
		$output = $this->controller->hashFolder(-1, ['title', 'url', 'tags']);
		$data2 = $output->getData();
		$this->assertEquals('success', $data2['status'], var_export($data2, true));

		$this->assertNotEquals($data2['data'], $data1['data']);

		// change sub folder
		$output = $this->controller->editFolder($this->folder1->getId(), 'blabla');
		$data = $output->getData();
		$this->assertEquals('success', $data['status'], var_export($data, true));

		// get hash for title,url
		$output = $this->controller->hashFolder(-1);
		$data1New = $output->getData();
		$this->assertEquals('success', $data1New['status'], var_export($data1New, true));

		// get hash for title,url,tags
		$output = $this->controller->hashFolder(-1, ['title', 'url', 'tags']);
		$data2New = $output->getData();
		$this->assertEquals('success', $data2New['status'], var_export($data2New, true));

		$this->assertNotEquals($data1New['data'], $data1['data']);
		$this->assertNotEquals($data2New['data'], $data2['data']);
		$this->assertNotEquals($data1New['data'], $data2New['data']);

		// check fail
		$output = $this->controller->hashFolder(-1, ['title', 'url', 'foo']);
		$data3 = $output->getData();
		$this->assertEquals('error', $data3['status'], var_export($data3, true));
	}

	/**
	 * @throws AlreadyExistsError
	 * @throws UrlParseError
	 * @throws UserLimitExceededError
	 * @throws \OCP\AppFramework\Db\DoesNotExistException
	 * @throws MultipleObjectsReturnedException
	 */
	public function testCreate(): void {

		$this->setupBookmarks();
		$this->authorizer->setUserId($this->userId);
		$output = $this->controller->addFolder('foo', $this->folder1->getId());
		$data = $output->getData();
		$this->assertEquals('success', $data['status'], var_export($data, true));
		$output = $this->controller->getFolder($data['item']['id']);
		$data = $output->getData();
		$this->assertEquals('success', $data['status'], var_export($data, true));
		$this->assertEquals('foo', $data['item']['title']);
	}

	/**
	 * @throws AlreadyExistsError
	 * @throws UrlParseError
	 * @throws UserLimitExceededError
	 * @throws \OCP\AppFramework\Db\DoesNotExistException
	 * @throws MultipleObjectsReturnedException
	 */
	public function testEdit(): void {
		$this->setupBookmarks();
		$this->authorizer->setUserId($this->userId);
		// Edit title
		$output = $this->controller->editFolder($this->folder1->getId(), 'blabla');
		$data = $output->getData();
		$this->assertEquals('success', $data['status'], var_export($data, true));
		$output = $this->controller->getFolder($this->folder1->getId());
		$data = $output->getData();
		$this->assertEquals('success', $data['status'], var_export($data, true));
		$this->assertEquals('blabla', $data['item']['title']);

		$output = $this->controller->getFolders();
		$topLevelFolders = array_map(fn ($item) => $item['id'], $output->getData()['data']);
		$this->assertEquals([$this->folder1->getId(), $this->folder3->getId()], $topLevelFolders);

		// Move folder
		$output = $this->controller->editFolder($this->folder1->getId(), 'blabla', $this->folder3->getId());
		$data = $output->getData();
		$this->assertEquals('success', $data['status'], var_export($data, true));
		$output = $this->controller->getFolder($this->folder1->getId());
		$data = $output->getData();
		$this->assertEquals('success', $data['status'], var_export($data, true));
		$this->assertEquals('blabla', $data['item']['title']);
		$this->assertEquals($this->folder3->getId(), $data['item']['parent_folder']);

		$output = $this->controller->getFolders();
		$topLevelFolders = array_map(fn ($item) => $item['id'], $output->getData()['data']);
		$this->assertEquals([$this->folder3->getId()], $topLevelFolders);
	}

	/**
	 * @throws AlreadyExistsError
	 * @throws UrlParseError
	 * @throws UserLimitExceededError
	 * @throws \OCP\AppFramework\Db\DoesNotExistException
	 * @throws MultipleObjectsReturnedException
	 * @dataProvider shareCanWriteDataProvider
	 */
	public function testSharedEdit(bool $canWrite): void {
		$this->setupBookmarks();
		$this->setupSharedFolder();
		$this->share->setCanWrite($canWrite);
		$this->shareMapper->update($this->share);

		$this->authorizer->setUserId($this->otherUserId);
		// Edit title
		$output = $this->otherController->editFolder($this->folder1->getId(), 'blabla');
		$data = $output->getData();
		$this->assertEquals('success', $data['status'], var_export($data, true));

		$output = $this->otherController->getFolder($this->folder1->getId());
		$data = $output->getData();
		$this->assertEquals('success', $data['status'], var_export($data, true));
		$this->assertEquals('blabla', $data['item']['title']);

		$output = $this->otherController->getFolders();
		$topLevelFolders = array_map(fn ($item) => $item['id'], $output->getData()['data']);
		$this->assertEquals([$this->folder4->getId(), $this->folder1->getId()], $topLevelFolders);

		// Move folder
		$output = $this->otherController->editFolder($this->folder1->getId(), 'blabla', $this->folder4->getId());
		$data = $output->getData();
		$this->assertEquals('success', $data['status'], var_export($data, true));
		$output = $this->otherController->getFolder($this->folder1->getId());
		$data = $output->getData();
		$this->assertEquals('success', $data['status'], var_export($data, true));
		$this->assertEquals('blabla', $data['item']['title']);
		$this->assertEquals($this->folder4->getId(), $data['item']['parent_folder']);

		$output = $this->otherController->getFolders();
		$topLevelFolders = array_map(fn ($item) => $item['id'], $output->getData()['data']);
		$this->assertEquals([$this->folder4->getId()], $topLevelFolders);
	}

	/**
	 * @throws AlreadyExistsError
	 * @throws UrlParseError
	 * @throws UserLimitExceededError
	 * @throws \OCP\AppFramework\Db\DoesNotExistException
	 * @throws MultipleObjectsReturnedException
	 */
	public function testDelete(): void {

		$this->setupBookmarks();
		$this->authorizer->setUserId($this->userId);
		$output = $this->controller->deleteFolder($this->folder1->getId(), true);
		$data = $output->getData();
		$this->assertEquals('success', $data['status'], var_export($data, true));
		$output = $this->controller->getFolder($this->folder1->getId());
		$data = $output->getData();
		$this->assertEquals('error', $data['status'], var_export($data, true));
	}

	/**
	 * @throws AlreadyExistsError
	 * @throws UrlParseError
	 * @throws UserLimitExceededError
	 * @throws MultipleObjectsReturnedException
	 */
	public function testGetFullHierarchy(): void {

		$this->setupBookmarks();
		$this->authorizer->setUserId($this->userId);
		// Using -1 here because this is the controller
		$output = $this->controller->getFolderChildrenOrder(-1, -1);
		$data = $output->getData();
		$this->assertEquals('success', $data['status'], var_export($data, true));
		$this->assertCount(2, $data['data']);
		$this->assertEquals($this->folder1->getId(), $data['data'][0]['id']);
		$this->assertEquals($this->folder3->getId(), $data['data'][1]['id']);
		$this->assertCount(2, $data['data'][0]['children']);
		$this->assertEquals($this->folder2->getId(), $data['data'][0]['children'][0]['id']);
		$this->assertEquals($this->bookmark1Id, $data['data'][0]['children'][1]['id']);
		$this->assertCount(1, $data['data'][0]['children'][0]['children']);
		$this->assertEquals($this->bookmark2Id, $data['data'][0]['children'][0]['children'][0]['id']);
	}

	/**
	 * @throws AlreadyExistsError
	 * @throws UrlParseError
	 * @throws UserLimitExceededError
	 * @throws MultipleObjectsReturnedException
	 */
	public function testSetFullHierarchy(): void {

		$this->setupBookmarks();
		$this->authorizer->setUserId($this->userId);
		$output = $this->controller->setFolderChildrenOrder($this->folder1->getId(), [
			['type' => 'bookmark', 'id' => $this->bookmark1Id],
			['type' => 'folder', 'id' => $this->folder2->getId()],
		]);
		$data = $output->getData();
		$this->assertEquals('success', $data['status'], var_export($data, true));
		$output = $this->controller->getFolderChildrenOrder(-1, -1);
		$data = $output->getData();
		$this->assertCount(2, $data['data']);
		$this->assertEquals($this->folder1->getId(), $data['data'][0]['id']);
		$this->assertEquals($this->folder3->getId(), $data['data'][1]['id']);
		$this->assertCount(2, $data['data'][0]['children']);
		$this->assertEquals($this->bookmark1Id, $data['data'][0]['children'][0]['id']);
		$this->assertEquals($this->folder2->getId(), $data['data'][0]['children'][1]['id']);
		$this->assertCount(1, $data['data'][0]['children'][1]['children']);
		$this->assertEquals($this->bookmark2Id, $data['data'][0]['children'][1]['children'][0]['id']);
	}

	/**
	 * @throws AlreadyExistsError
	 * @throws UrlParseError
	 * @throws UserLimitExceededError
	 * @throws MultipleObjectsReturnedException
	 */
	public function testGetFolderHierarchy(): void {

		$this->setupBookmarks();
		$this->authorizer->setUserId($this->userId);
		$output = $this->controller->getFolders(-1, -1);
		$data = $output->getData();
		$this->assertEquals('success', $data['status'], var_export($data, true));
		$this->assertCount(2, $data['data']);
		$this->assertEquals('foo', $data['data'][0]['title']);
		$this->assertCount(1, $data['data'][0]['children']);
		$this->assertEquals('bar', $data['data'][1]['title']);
		$this->assertCount(0, $data['data'][1]['children']);
		$this->assertEquals('bar', $data['data'][0]['children'][0]['title']);
		$this->assertCount(0, $data['data'][0]['children'][0]['children']);
	}

	/**
	 * @throws AlreadyExistsError
	 * @throws UrlParseError
	 * @throws UserLimitExceededError
	 * @throws \OCP\AppFramework\Db\DoesNotExistException
	 * @throws MultipleObjectsReturnedException
	 */
	public function testReadNoauthFail(): void {

		$this->setupBookmarks();
		$this->setupPublicFolder();
		$this->authorizer->setUserId(null);
		$this->authorizer->setToken(null);
		$this->expectException(UnauthenticatedError::class);
		$this->noauth->getFolder($this->folder1->getId());
	}

	/**
	 * @throws AlreadyExistsError
	 * @throws UrlParseError
	 * @throws UserLimitExceededError
	 * @throws MultipleObjectsReturnedException
	 */
	public function testCreateNoauthFail(): void {

		$this->setupBookmarks();
		$this->setupPublicFolder();
		$this->authorizer->setUserId(null);
		$this->authorizer->setToken(null);
		$this->expectException(UnauthenticatedError::class);
		$this->noauth->addFolder('bla', $this->folder1->getId());
	}

	/**
	 * @throws AlreadyExistsError
	 * @throws UrlParseError
	 * @throws UserLimitExceededError
	 * @throws \OCP\AppFramework\Db\DoesNotExistException
	 * @throws MultipleObjectsReturnedException
	 */
	public function testEditNoauthFail(): void {

		$this->setupBookmarks();
		$this->setupPublicFolder();
		$this->authorizer->setUserId(null);
		$this->authorizer->setToken(null);
		$this->expectException(UnauthenticatedError::class);
		$this->noauth->editFolder($this->folder2->getId(), 'blabla');
	}

	/**
	 * @throws AlreadyExistsError
	 * @throws UrlParseError
	 * @throws UserLimitExceededError
	 * @throws \OCP\AppFramework\Db\DoesNotExistException
	 * @throws MultipleObjectsReturnedException
	 */
	public function testDeleteNoauthFail(): void {

		$this->setupBookmarks();
		$this->setupPublicFolder();
		$this->authorizer->setUserId(null);
		$this->authorizer->setToken(null);
		$this->expectException(UnauthenticatedError::class);
		$this->noauth->deleteFolder($this->folder1->getId());
	}

	/**
	 * @throws AlreadyExistsError
	 * @throws UrlParseError
	 * @throws UserLimitExceededError
	 * @throws MultipleObjectsReturnedException
	 */
	public function testGetFullHierarchyNoauthFail(): void {

		$this->setupBookmarks();
		$this->authorizer->setUserId(null);
		$this->authorizer->setToken(null);
		$this->expectException(UnauthenticatedError::class);
		$this->noauth->getFolderChildrenOrder($this->folder1->getId(), -1);
	}

	/**
	 * @throws AlreadyExistsError
	 * @throws UrlParseError
	 * @throws UserLimitExceededError
	 * @throws MultipleObjectsReturnedException
	 */
	public function testSetFullHierarchyNoauthFail(): void {

		$this->setupBookmarks();
		$this->authorizer->setUserId(null);
		$this->authorizer->setToken(null);
		$this->expectException(UnauthenticatedError::class);
		$this->noauth->setFolderChildrenOrder($this->folder1->getId(), [
			['type' => 'noauth', 'id' => $this->bookmark1Id],
			['type' => 'folder', 'id' => $this->folder2->getId()],
		]);
	}

	/**
	 * @throws AlreadyExistsError
	 * @throws UrlParseError
	 * @throws UserLimitExceededError
	 * @throws MultipleObjectsReturnedException
	 */
	public function testGetFolderHierarchyNoauth(): void {

		$this->setupBookmarks();
		$this->authorizer->setUserId(null);
		$this->authorizer->setToken(null);
		$this->expectException(UnauthenticatedError::class);
		$this->noauth->getFolders($this->folder1->getId(), -1);
	}

	/**
	 * @throws AlreadyExistsError
	 * @throws UrlParseError
	 * @throws UserLimitExceededError
	 * @throws \OCP\AppFramework\Db\DoesNotExistException
	 * @throws MultipleObjectsReturnedException
	 */
	public function testReadPublic(): void {

		$this->setupBookmarks();
		$this->setupPublicFolder();
		$this->authorizer->setUserId(null);
		$output = $this->public->getFolder($this->folder1->getId());
		$data = $output->getData();
		$this->assertEquals('success', $data['status'], var_export($data, true));
		$this->assertEquals($this->folder1->getTitle(), $data['item']['title']);
	}

	/**
	 * @throws AlreadyExistsError
	 * @throws UrlParseError
	 * @throws UserLimitExceededError
	 * @throws \OCP\AppFramework\Db\DoesNotExistException
	 * @throws MultipleObjectsReturnedException
	 */
	public function testReadPublicFail(): void {
		$this->setupBookmarks();
		$this->authorizer->setUserId(null);
		$this->authorizer->setToken('foobar');
		$output = $this->public->getFolder($this->folder1->getId());
		$data = $output->getData();
		$this->assertEquals('error', $data['status'], var_export($data, true));
	}

	/**
	 * @throws AlreadyExistsError
	 * @throws UrlParseError
	 * @throws UserLimitExceededError
	 * @throws MultipleObjectsReturnedException
	 */
	public function testCreatePublicFail(): void {
		$this->setupBookmarks();
		$this->setupPublicFolder();
		$this->authorizer->setUserId(null);
		$output = $this->public->addFolder('bla', $this->folder1->getId());
		$data = $output->getData();
		$this->assertEquals('error', $data['status'], var_export($data, true));
	}

	/**
	 * @throws AlreadyExistsError
	 * @throws UrlParseError
	 * @throws UserLimitExceededError
	 * @throws \OCP\AppFramework\Db\DoesNotExistException
	 * @throws MultipleObjectsReturnedException
	 */
	public function testEditPublicFail(): void {

		$this->setupBookmarks();
		$this->setupPublicFolder();
		$this->authorizer->setUserId(null);
		$output = $this->public->editFolder($this->folder2->getId(), 'blabla');
		$data = $output->getData();
		$this->assertEquals('error', $data['status'], var_export($data, true));
		$output = $this->public->getFolder($this->folder2->getId());
		$data = $output->getData();
		$this->assertEquals('success', $data['status'], var_export($data, true));
		$this->assertEquals($this->folder2->getTitle(), $data['item']['title']); // nothing changed
	}

	/**
	 * @throws AlreadyExistsError
	 * @throws UrlParseError
	 * @throws UserLimitExceededError
	 * @throws \OCP\AppFramework\Db\DoesNotExistException
	 * @throws MultipleObjectsReturnedException
	 */
	public function testDeletePublicFail(): void {

		$this->setupBookmarks();
		$this->setupPublicFolder();
		$this->authorizer->setUserId(null);
		$output = $this->public->deleteFolder($this->folder1->getId());
		$data = $output->getData();
		$this->assertEquals('success', $data['status'], var_export($data, true));
		$output = $this->public->getFolder($this->folder1->getId());
		$data = $output->getData();
		$this->assertEquals('success', $data['status'], var_export($data, true));
	}

	/**
	 * @throws AlreadyExistsError
	 * @throws UrlParseError
	 * @throws UserLimitExceededError
	 * @throws MultipleObjectsReturnedException
	 */
	public function testGetFullHierarchyPublic(): void {

		$this->setupBookmarks();
		$this->setupPublicFolder();
		$this->authorizer->setUserId(null);
		$output = $this->public->getFolderChildrenOrder($this->folder1->getId(), -1);
		$data = $output->getData();
		$this->assertEquals('success', $data['status'], var_export($data, true));
		$this->assertCount(2, $data['data']);
		$this->assertEquals($this->folder2->getId(), $data['data'][0]['id']);
		$this->assertEquals($this->bookmark1Id, $data['data'][1]['id']);
		$this->assertCount(1, $data['data'][0]['children']);
		$this->assertEquals($this->bookmark2Id, $data['data'][0]['children'][0]['id']);
	}

	/**
	 * @throws AlreadyExistsError
	 * @throws UrlParseError
	 * @throws UserLimitExceededError
	 * @throws MultipleObjectsReturnedException
	 */
	public function testSetFullHierarchyPublicFail(): void {
		$this->setupBookmarks();
		$this->setupPublicFolder();

		$this->authorizer->setUserId($this->userId);
		$originalOutput = $this->controller->getFolderChildrenOrder(-1, -1)->getData();

		$this->authorizer->setUserId(null);
		$output = $this->public->setFolderChildrenOrder($this->folder1->getId(), [
			['type' => 'bookmark', 'id' => $this->bookmark1Id],
			['type' => 'folder', 'id' => $this->folder2->getId()],
		]);
		$data = $output->getData();
		$this->assertEquals('error', $data['status'], var_export($data, true));

		$this->authorizer->setUserId($this->userId);
		$this->authorizer->setToken(null);
		$afterOutput = $this->controller->getFolderChildrenOrder(-1, -1)->getData();
		$this->assertEquals('success', $afterOutput['status'], var_export($afterOutput, true));
		$this->assertEquals($originalOutput['data'], $afterOutput['data']);
	}

	/**
	 * @throws AlreadyExistsError
	 * @throws UrlParseError
	 * @throws UserLimitExceededError
	 * @throws MultipleObjectsReturnedException
	 */
	public function testGetFolderHierarchyPublic(): void {

		$this->setupBookmarks();
		$this->setupPublicFolder();
		$this->authorizer->setUserId(null);
		$output = $this->public->getFolders($this->folder1->getId(), -1);
		$data = $output->getData();
		$this->assertCount(1, $data['data'], var_export($data['data'], true));
		$this->assertEquals('bar', $data['data'][0]['title']);
		$this->assertCount(0, $data['data'][0]['children']);
	}

	/**
	 * @throws AlreadyExistsError
	 * @throws UrlParseError
	 * @throws UserLimitExceededError
	 * @throws \OCP\AppFramework\Db\DoesNotExistException
	 * @throws MultipleObjectsReturnedException
	 */
	public function testReadShared(): void {

		$this->setupBookmarks();
		$this->setupSharedFolder();
		$this->authorizer->setUserId($this->otherUserId);
		$output = $this->otherController->getFolder($this->folder1->getId());
		$data = $output->getData();
		$this->assertEquals('success', $data['status'], var_export($data, true));
		$this->assertEquals($this->folder1->getTitle(), $data['item']['title']);
	}

	/**
	 * @throws AlreadyExistsError
	 * @throws UrlParseError
	 * @throws UserLimitExceededError
	 * @throws \OCP\AppFramework\Db\DoesNotExistException
	 * @throws MultipleObjectsReturnedException
	 */
	public function testReadSharedFail(): void {

		$this->setupBookmarks();
		$this->authorizer->setUserId($this->otherUserId);
		$output = $this->otherController->getFolder($this->folder1->getId());
		$data = $output->getData();
		$this->assertEquals('error', $data['status'], var_export($data, true));
	}

	/**
	 * @throws AlreadyExistsError
	 * @throws UrlParseError
	 * @throws UserLimitExceededError
	 * @throws \OCP\AppFramework\Db\DoesNotExistException
	 * @throws MultipleObjectsReturnedException
	 */
	public function testCreateShared(): void {

		$this->setupBookmarks();
		$this->setupSharedFolder();
		$this->authorizer->setUserId($this->otherUserId);
		$output = $this->otherController->addFolder('bla', $this->folder1->getId());
		$data = $output->getData();
		$this->assertEquals('success', $data['status'], var_export($data, true));
		$output = $this->otherController->getFolder($data['item']['id']);
		$data = $output->getData();
		$this->assertEquals('success', $data['status'], var_export($data, true));
	}

	/**
	 * @throws AlreadyExistsError
	 * @throws UrlParseError
	 * @throws UserLimitExceededError
	 * @throws \OCP\AppFramework\Db\DoesNotExistException
	 * @throws MultipleObjectsReturnedException
	 * @dataProvider shareCanWriteDataProvider
	 */
	public function testEditShared(bool $canWrite): void {
		$this->setupBookmarks();
		$this->setupSharedFolder();
		$this->share->setCanWrite($canWrite);
		$this->shareMapper->update($this->share);

		$this->authorizer->setUserId($this->otherUserId);
		$output = $this->otherController->editFolder($this->folder2->getId(), 'blabla');
		$data = $output->getData();
		if ($canWrite) {
			$this->assertEquals('success', $data['status'], var_export($data, true));
		} else {
			$this->assertEquals('error', $data['status'], var_export($data, true)); // Didn't go through
		}
		$output = $this->otherController->getFolder($this->folder2->getId());
		$data = $output->getData();
		$this->assertEquals('success', $data['status'], var_export($data, true));
		if ($canWrite) {
			$this->assertEquals('blabla', $data['item']['title']);
		} else {
			$this->assertEquals('bar', $data['item']['title']); // didn't go through
		}
	}

	/**
	 * @throws AlreadyExistsError
	 * @throws UrlParseError
	 * @throws UserLimitExceededError
	 * @throws \OCP\AppFramework\Db\DoesNotExistException
	 * @throws MultipleObjectsReturnedException
	 */
	public function testDeleteShared(): void {

		$this->setupBookmarks();
		$this->setupSharedFolder();
		$this->authorizer->setUserId($this->otherUserId);
		$output = $this->otherController->deleteFolder($this->folder1->getId(), true);
		$data = $output->getData();
		$this->assertEquals('success', $data['status'], var_export($data, true));
		$output = $this->otherController->getFolder($this->folder1->getId());
		$data = $output->getData();
		$this->assertEquals('error', $data['status'], var_export($data, true));
	}

	/**
	 * @throws AlreadyExistsError
	 * @throws UrlParseError
	 * @throws UserLimitExceededError
	 * @throws \OCP\AppFramework\Db\DoesNotExistException
	 * @throws MultipleObjectsReturnedException
	 * @dataProvider shareCanWriteDataProvider
	 */
	public function testDeleteFromSharedFolder(bool $canWrite): void {
		$this->setupBookmarks();
		$this->setupSharedFolder($canWrite);
		$this->authorizer->setUserId($this->otherUserId);
		$output = $this->otherController->removeFromFolder($this->folder1->getId(), $this->bookmark1Id);
		$data = $output->getData();
		if ($canWrite) {
			$this->assertEquals('success', $data['status'], var_export($data, true));
		} else {
			$this->assertEquals('error', $data['status'], var_export($data, true));
		}
	}

	/**
	 * @throws AlreadyExistsError
	 * @throws UrlParseError
	 * @throws UserLimitExceededError
	 * @throws MultipleObjectsReturnedException
	 */
	public function testGetFullHierarchyShared(): void {

		$this->setupBookmarks();
		$this->setupSharedFolder();
		$this->authorizer->setUserId($this->otherUserId);
		$output = $this->otherController->getFolderChildrenOrder($this->folder1->getId(), -1);
		$data = $output->getData();
		$this->assertEquals('success', $data['status'], var_export($data, true));
		$this->assertCount(2, $data['data']);
		$this->assertEquals($this->folder2->getId(), $data['data'][0]['id']);
		$this->assertEquals($this->bookmark1Id, $data['data'][1]['id']);
		$this->assertCount(1, $data['data'][0]['children']);
		$this->assertEquals($this->bookmark2Id, $data['data'][0]['children'][0]['id']);
	}

	/**
	 * @throws AlreadyExistsError
	 * @throws UrlParseError
	 * @throws UserLimitExceededError
	 * @throws MultipleObjectsReturnedException
	 */
	public function testSetFullHierarchyShared(): void {

		$this->setupBookmarks();
		$this->setupSharedFolder();

		$this->authorizer->setUserId($this->otherUserId);
		$output = $this->otherController->setFolderChildrenOrder($this->folder1->getId(), [
			['type' => 'bookmark', 'id' => $this->bookmark1Id],
			['type' => 'folder', 'id' => $this->folder2->getId()],
		]);
		$data = $output->getData();
		$this->assertEquals('success', $data['status'], var_export($data, true));

		$this->authorizer->setUserId($this->userId);
		$output = $this->controller->getFolderChildrenOrder(-1, -1);
		$data = $output->getData();
		$this->assertCount(2, $data['data']);
		$this->assertEquals($this->folder1->getId(), $data['data'][0]['id']);
		$this->assertEquals($this->folder3->getId(), $data['data'][1]['id']);
		$this->assertCount(2, $data['data'][0]['children']);
		$this->assertEquals($this->bookmark1Id, $data['data'][0]['children'][0]['id']);
		$this->assertEquals($this->folder2->getId(), $data['data'][0]['children'][1]['id']);
		$this->assertCount(1, $data['data'][0]['children'][1]['children']);
		$this->assertEquals($this->bookmark2Id, $data['data'][0]['children'][1]['children'][0]['id']);
	}

	/**
	 * @throws AlreadyExistsError
	 * @throws UrlParseError
	 * @throws UserLimitExceededError
	 * @throws MultipleObjectsReturnedException
	 */
	public function testGetFolderHierarchyShared(): void {

		$this->setupBookmarks();
		$this->setupSharedFolder();
		$this->authorizer->setUserId($this->otherUserId);
		$output = $this->otherController->getFolders($this->folder1->getId(), -1);
		$data = $output->getData();
		$this->assertEquals('success', $data['status'], var_export($data, true));
		$this->assertCount(1, $data['data']);
		$this->assertEquals('bar', $data['data'][0]['title']);
		$this->assertCount(0, $data['data'][0]['children']);
	}

	/**
	 * @param $participant
	 * @param $type
	 * @param $canWrite
	 * @param $canShare
	 * @throws AlreadyExistsError
	 * @throws UrlParseError
	 * @throws UserLimitExceededError
	 * @throws MultipleObjectsReturnedException
	 * @dataProvider shareDataProvider
	 */
	public function testCreateShare($participant, $type, $canWrite, $canShare): void {

		$this->setupBookmarks();
		$this->authorizer->setUserid($this->userId);
		$res = $this->controller->createShare($this->folder1->getId(), $participant, $type, $canWrite, $canShare);
		$data = $res->getData();
		$this->assertEquals('success', $data['status'], var_export($data, true));
	}

	/**
	 * @param $participant
	 * @param $type
	 * @param $canWrite
	 * @param $canShare
	 * @throws AlreadyExistsError
	 * @throws UrlParseError
	 * @throws UserLimitExceededError
	 * @throws MultipleObjectsReturnedException
	 * @dataProvider shareDataProvider
	 * @depends      testCreateShare
	 */
	public function testGetShare($participant, $type, $canWrite, $canShare): void {

		$this->setupBookmarks();
		$this->authorizer->setUserId($this->userId);
		$res = $this->controller->createShare($this->folder1->getId(), $participant, $type, $canWrite, $canShare);
		$data = $res->getData();
		$this->assertEquals('success', $data['status'], var_export($data, true));
		$res = $this->controller->getShare($data['item']['id']);
		$data = $res->getData();
		$this->assertEquals('success', $data['status'], var_export($data, true));
		$this->authorizer->setUserId($this->otherUserId);
		$res = $this->otherController->getShare($data['item']['id']);
		$data = $res->getData();
		$this->assertEquals('success', $data['status'], var_export($data, true));
	}

	/**
	 * @param $participant
	 * @param $type
	 * @param $canWrite
	 * @param $canShare
	 * @throws AlreadyExistsError
	 * @throws UrlParseError
	 * @throws UserLimitExceededError
	 * @throws MultipleObjectsReturnedException
	 * @dataProvider shareDataProvider
	 * @depends      testCreateShare
	 */
	public function testEditShare($participant, $type, $canWrite, $canShare): void {

		$this->setupBookmarks();
		$this->authorizer->setUserId($this->userId);
		$res = $this->controller->createShare($this->folder1->getId(), $participant, $type, $canWrite, $canShare);
		$data = $res->getData();
		$this->assertEquals('success', $data['status'], var_export($data, true));
		$shareId = $data['item']['id'];

		$this->authorizer->setUserId($this->otherUserId);
		$res = $this->otherController->editShare($shareId, false, false);
		$data = $res->getData();
		if ($canShare) {
			$this->assertEquals('success', $data['status'], var_export($data, true));
		} else {
			$this->assertEquals('error', $data['status'], var_export($data, true));
		}

		$this->authorizer->setUserId($this->userId);
		$res = $this->controller->editShare($shareId, false, false);
		$data = $res->getData();
		$this->assertEquals('success', $data['status'], var_export($data, true));
	}

	/**
	 * @param $participant
	 * @param $type
	 * @param $canWrite
	 * @param $canShare
	 * @throws AlreadyExistsError
	 * @throws UrlParseError
	 * @throws UserLimitExceededError
	 * @throws MultipleObjectsReturnedException
	 * @dataProvider shareDataProvider
	 * @depends      testCreateShare
	 */
	public function testDeleteShareOwner($participant, $type, $canWrite, $canShare): void {

		$this->setupBookmarks();
		$this->authorizer->setUserId($this->userId);
		$res = $this->controller->createShare($this->folder1->getId(), $participant, $type, $canWrite, $canShare);
		$data = $res->getData();
		$this->assertEquals('success', $data['status'], var_export($data, true));
		$shareId = $data['item']['id'];

		$this->authorizer->setUserId($this->userId);
		$res = $this->controller->deleteShare($shareId);
		$data = $res->getData();
		$this->assertEquals('success', $data['status'], var_export($data, true));
	}

	/**
	 * @param $participant
	 * @param $type
	 * @param $canWrite
	 * @param $canShare
	 * @throws AlreadyExistsError
	 * @throws UrlParseError
	 * @throws UserLimitExceededError
	 * @throws MultipleObjectsReturnedException
	 * @dataProvider shareDataProvider
	 * @depends      testCreateShare
	 */
	public function testDeleteShareSharee($participant, $type, $canWrite, $canShare): void {

		$this->setupBookmarks();
		$this->authorizer->setUserId($this->userId);
		$res = $this->controller->createShare($this->folder1->getId(), $participant, $type, $canWrite, $canShare);
		$data = $res->getData();
		$this->assertEquals('success', $data['status'], var_export($data, true));
		$shareId = $data['item']['id'];

		$this->authorizer->setUserId($this->otherUserId);
		$res = $this->otherController->deleteShare($shareId);
		$data = $res->getData();
		if ($canShare) {
			$this->assertEquals('success', $data['status'], var_export($data, true));
		} else {
			$this->assertEquals('success', $data['status'], var_export($data, true));
			$this->shareMapper->find($shareId);
		}
	}

	/**
	 * @return array
	 */
	public function shareDataProvider(): array {
		return [
			['otheruser', \OCP\Share\IShare::TYPE_USER, true, false],
			['otheruser', \OCP\Share\IShare::TYPE_USER, true, true],
			['foobar', \OCP\Share\IShare::TYPE_GROUP, true, false],
			['foobar', \OCP\Share\IShare::TYPE_GROUP, true, true],
		];
	}

	public function hashCacheDataProvider(): array {
		return [
			[false],
			[true],
		];
	}

	public function shareCanWriteDataProvider(): array {
		return [
			[false],
			[true],
		];
	}
}
