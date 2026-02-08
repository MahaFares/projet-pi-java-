<?php

namespace App\Tests\Controller;

use App\Entity\Guide;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class GuideControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $guideRepository;
    private string $path = '/guide/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->guideRepository = $this->manager->getRepository(Guide::class);

        foreach ($this->guideRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Guide index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first()->text());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'guide[firstName]' => 'Testing',
            'guide[lastName]' => 'Testing',
            'guide[email]' => 'Testing',
            'guide[phone]' => 'Testing',
            'guide[bio]' => 'Testing',
            'guide[rating]' => 'Testing',
            'guide[photo]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->guideRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Guide();
        $fixture->setFirstName('My Title');
        $fixture->setLastName('My Title');
        $fixture->setEmail('My Title');
        $fixture->setPhone('My Title');
        $fixture->setBio('My Title');
        $fixture->setRating('My Title');
        $fixture->setPhoto('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Guide');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Guide();
        $fixture->setFirstName('Value');
        $fixture->setLastName('Value');
        $fixture->setEmail('Value');
        $fixture->setPhone('Value');
        $fixture->setBio('Value');
        $fixture->setRating('Value');
        $fixture->setPhoto('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'guide[firstName]' => 'Something New',
            'guide[lastName]' => 'Something New',
            'guide[email]' => 'Something New',
            'guide[phone]' => 'Something New',
            'guide[bio]' => 'Something New',
            'guide[rating]' => 'Something New',
            'guide[photo]' => 'Something New',
        ]);

        self::assertResponseRedirects('/guide/');

        $fixture = $this->guideRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getFirstName());
        self::assertSame('Something New', $fixture[0]->getLastName());
        self::assertSame('Something New', $fixture[0]->getEmail());
        self::assertSame('Something New', $fixture[0]->getPhone());
        self::assertSame('Something New', $fixture[0]->getBio());
        self::assertSame('Something New', $fixture[0]->getRating());
        self::assertSame('Something New', $fixture[0]->getPhoto());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Guide();
        $fixture->setFirstName('Value');
        $fixture->setLastName('Value');
        $fixture->setEmail('Value');
        $fixture->setPhone('Value');
        $fixture->setBio('Value');
        $fixture->setRating('Value');
        $fixture->setPhoto('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/guide/');
        self::assertSame(0, $this->guideRepository->count([]));
    }
}
