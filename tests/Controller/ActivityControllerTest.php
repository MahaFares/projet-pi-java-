<?php

namespace App\Tests\Controller;

use App\Entity\Activity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ActivityControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $activityRepository;
    private string $path = '/activity/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->activityRepository = $this->manager->getRepository(Activity::class);

        foreach ($this->activityRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Activity index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first()->text());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'activity[title]' => 'Testing',
            'activity[description]' => 'Testing',
            'activity[price]' => 'Testing',
            'activity[durationMinutes]' => 'Testing',
            'activity[location]' => 'Testing',
            'activity[maxParticipants]' => 'Testing',
            'activity[image]' => 'Testing',
            'activity[isActive]' => 'Testing',
            'activity[category]' => 'Testing',
            'activity[guide]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->activityRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Activity();
        $fixture->setTitle('My Title');
        $fixture->setDescription('My Title');
        $fixture->setPrice('My Title');
        $fixture->setDurationMinutes('My Title');
        $fixture->setLocation('My Title');
        $fixture->setMaxParticipants('My Title');
        $fixture->setImage('My Title');
        $fixture->setIsActive('My Title');
        $fixture->setCategory('My Title');
        $fixture->setGuide('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Activity');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Activity();
        $fixture->setTitle('Value');
        $fixture->setDescription('Value');
        $fixture->setPrice('Value');
        $fixture->setDurationMinutes('Value');
        $fixture->setLocation('Value');
        $fixture->setMaxParticipants('Value');
        $fixture->setImage('Value');
        $fixture->setIsActive('Value');
        $fixture->setCategory('Value');
        $fixture->setGuide('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'activity[title]' => 'Something New',
            'activity[description]' => 'Something New',
            'activity[price]' => 'Something New',
            'activity[durationMinutes]' => 'Something New',
            'activity[location]' => 'Something New',
            'activity[maxParticipants]' => 'Something New',
            'activity[image]' => 'Something New',
            'activity[isActive]' => 'Something New',
            'activity[category]' => 'Something New',
            'activity[guide]' => 'Something New',
        ]);

        self::assertResponseRedirects('/activity/');

        $fixture = $this->activityRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getTitle());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getPrice());
        self::assertSame('Something New', $fixture[0]->getDurationMinutes());
        self::assertSame('Something New', $fixture[0]->getLocation());
        self::assertSame('Something New', $fixture[0]->getMaxParticipants());
        self::assertSame('Something New', $fixture[0]->getImage());
        self::assertSame('Something New', $fixture[0]->getIsActive());
        self::assertSame('Something New', $fixture[0]->getCategory());
        self::assertSame('Something New', $fixture[0]->getGuide());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Activity();
        $fixture->setTitle('Value');
        $fixture->setDescription('Value');
        $fixture->setPrice('Value');
        $fixture->setDurationMinutes('Value');
        $fixture->setLocation('Value');
        $fixture->setMaxParticipants('Value');
        $fixture->setImage('Value');
        $fixture->setIsActive('Value');
        $fixture->setCategory('Value');
        $fixture->setGuide('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/activity/');
        self::assertSame(0, $this->activityRepository->count([]));
    }
}
