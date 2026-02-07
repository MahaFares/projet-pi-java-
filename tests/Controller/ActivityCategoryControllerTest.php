<?php

namespace App\Tests\Controller;

use App\Entity\ActivityCategory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ActivityCategoryControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $activityCategoryRepository;
    private string $path = '/activity/category/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->activityCategoryRepository = $this->manager->getRepository(ActivityCategory::class);

        foreach ($this->activityCategoryRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('ActivityCategory index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first()->text());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'activity_category[name]' => 'Testing',
            'activity_category[description]' => 'Testing',
            'activity_category[icon]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->activityCategoryRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new ActivityCategory();
        $fixture->setName('My Title');
        $fixture->setDescription('My Title');
        $fixture->setIcon('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('ActivityCategory');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new ActivityCategory();
        $fixture->setName('Value');
        $fixture->setDescription('Value');
        $fixture->setIcon('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'activity_category[name]' => 'Something New',
            'activity_category[description]' => 'Something New',
            'activity_category[icon]' => 'Something New',
        ]);

        self::assertResponseRedirects('/activity/category/');

        $fixture = $this->activityCategoryRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getIcon());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new ActivityCategory();
        $fixture->setName('Value');
        $fixture->setDescription('Value');
        $fixture->setIcon('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/activity/category/');
        self::assertSame(0, $this->activityCategoryRepository->count([]));
    }
}
