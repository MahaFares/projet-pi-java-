<?php

namespace App\Tests\Controller;

use App\Entity\ActivitySchedule;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ActivityScheduleControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $activityScheduleRepository;
    private string $path = '/activity/schedule/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->activityScheduleRepository = $this->manager->getRepository(ActivitySchedule::class);

        foreach ($this->activityScheduleRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('ActivitySchedule index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first()->text());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'activity_schedule[startAt]' => 'Testing',
            'activity_schedule[endAt]' => 'Testing',
            'activity_schedule[availableSpots]' => 'Testing',
            'activity_schedule[activity]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->activityScheduleRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new ActivitySchedule();
        $fixture->setStartAt('My Title');
        $fixture->setEndAt('My Title');
        $fixture->setAvailableSpots('My Title');
        $fixture->setActivity('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('ActivitySchedule');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new ActivitySchedule();
        $fixture->setStartAt('Value');
        $fixture->setEndAt('Value');
        $fixture->setAvailableSpots('Value');
        $fixture->setActivity('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'activity_schedule[startAt]' => 'Something New',
            'activity_schedule[endAt]' => 'Something New',
            'activity_schedule[availableSpots]' => 'Something New',
            'activity_schedule[activity]' => 'Something New',
        ]);

        self::assertResponseRedirects('/activity/schedule/');

        $fixture = $this->activityScheduleRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getStartAt());
        self::assertSame('Something New', $fixture[0]->getEndAt());
        self::assertSame('Something New', $fixture[0]->getAvailableSpots());
        self::assertSame('Something New', $fixture[0]->getActivity());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new ActivitySchedule();
        $fixture->setStartAt('Value');
        $fixture->setEndAt('Value');
        $fixture->setAvailableSpots('Value');
        $fixture->setActivity('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/activity/schedule/');
        self::assertSame(0, $this->activityScheduleRepository->count([]));
    }
}
