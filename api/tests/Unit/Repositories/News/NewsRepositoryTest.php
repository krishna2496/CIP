<?php

namespace Tests\Unit\Repositories\News;

use App\Helpers\Helpers;
use App\Helpers\LanguageHelper;
use App\Helpers\S3Helper;
use App\Models\News;
use App\Models\NewsLanguage;
use App\Models\NewsToCategory;
use App\Repositories\News\NewsRepository;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Mockery;
use StdClass;
use TestCase;

// use App\Events\User\UserActivityLogEvent;
// use App\Helpers\ResponseHelper;
// use App\Http\Controllers\Admin\Slider\SliderController;
// use App\Models\Slider;
// use App\Repositories\News\NewsInterface;
// use App\Repositories\Slider\SliderRepository;
// use Exception;
// use Illuminate\Database\Eloquent\Collection;
// use Illuminate\Database\Eloquent\ModelNotFoundException;
// use Illuminate\Http\Client\Request as ClientRequest;
// use Illuminate\Http\JsonResponse;
// use Illuminate\Http\Request;
// use Illuminate\Http\Response;
// use Illuminate\Pagination\LengthAwarePaginator;
// use Illuminate\Support\Collection;
// use Illuminate\Support\MessageBag;
// use Illuminate\Validation\Validator as TrueValidator;
// use InvalidArgumentException;
// use Validator;

class NewsRepositoryTest extends TestCase
{
	public function setUp(): void
	{
		parent::setUp();
		$this->faker = FakerFactory::create();
		$this->generateMocks();
	}

	public function testGetNewsTitle()
	{
		$newsId = 1;
		$languageId = 2;
		$defaultTenantLanguageId = 3;

		$anything = $this->mockAnything()
			->setMethods([
				'count',
				'first',
				'get',
				'select',
				'toArray',
				'where',
			])
			->disableOriginalConstructor()
			->getMock();

		$anything
			->expects($this->once())
			->method('select')
			->with('title', 'language_id')
			->willReturn($anything);

		$anything
			->expects($this->exactly(2))
			->method('where')
			->withConsecutive(
				[['news_id' => $newsId]],
				['language_id', $languageId],
			)
			->willReturn($anything);

		$anything
			->expects($this->once())
			->method('get')
			->willReturn($anything);

		$anything
			->expects($this->once())
			->method('count')
			->willReturn(1);  // as long as greater than 0

		$anything
			->expects($this->once())
			->method('toArray')
			->willReturn([
				['language_id' => $languageId],
			]);

		$anything
			->expects($this->once())
			->method('first')
			->willReturn($this->news);

		$this->newsLanguage = $this->newsLanguage
			->setMethods(['withTrashed'])
			->disableOriginalConstructor()
			->getMock();

		$this->newsLanguage
			->expects($this->once())
			->method('withTrashed')
			->willReturn($anything);

		$newsRepository = $this->getNewsRepositoryMock();
		$result = $newsRepository->getNewsTitle($newsId, $languageId, $defaultTenantLanguageId);

		$this->assertSame('news today', $result);
	}

	public function testGetNewsTitleNoResult()
	{
		$newsId = 1;
		$languageId = 2;
		$defaultTenantLanguageId = 3;

		$anything = $this->mockAnything()
			->setMethods([
				'count',
				'first',
				'get',
				'select',
				'toArray',
				'where',
			])
			->disableOriginalConstructor()
			->getMock();

		$anything
			->expects($this->once())
			->method('select')
			->with('title', 'language_id')
			->willReturn($anything);

		$anything
			->expects($this->once())
			->method('where')
			->with(['news_id' => $newsId])
			->willReturn($anything);

		$anything
			->expects($this->once())
			->method('get')
			->willReturn($anything);

		$anything
			->expects($this->once())
			->method('count')
			->willReturn(0);  // no entry

		$anything
			->expects($this->never())
			->method('toArray');

		$anything
			->expects($this->never())
			->method('first');

		$this->newsLanguage = $this->newsLanguage
			->setMethods(['withTrashed'])
			->disableOriginalConstructor()
			->getMock();

		$this->newsLanguage
			->expects($this->once())
			->method('withTrashed')
			->willReturn($anything);

		$newsRepository = $this->getNewsRepositoryMock();
		$result = $newsRepository->getNewsTitle($newsId, $languageId, $defaultTenantLanguageId);

		$this->assertSame('', $result);
	}

	private function getNewsRepositoryMock()
	{
		return new NewsRepository(
			$this->news,
			$this->newsToCategory,
			$this->newsLanguage,
			$this->languageHelper,
			$this->helpers,
			$this->s3Helper
		);
	}

	private function mockAnything($className = null)
	{
		if ($className) {
			return $this->getMockBuilder($className);
		} else {
			return $this->getMockBuilder(StdClass::class);
		}
	}

	private function generateMocks()
	{
		$this->helpers = $this->createMock(Helpers::class);
		$this->languageHelper = $this->createMock(LanguageHelper::class);
		// $this->news = $this->createMock(News::class);
		$this->news = new News;
		$this->news->title = 'news today';
		$this->newsLanguage = $this->mockAnything(NewsLanguage::class);
		$this->newsToCategory = $this->createMock(NewsToCategory::class);
		$this->s3Helper = $this->createMock(S3Helper::class);
	}
}
