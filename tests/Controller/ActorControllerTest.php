<?php

namespace App\Test\Controller;

use App\Entity\Actor;
use App\Repository\ActorRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ActorControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private ActorRepository $repository;
    private string $path = '/actor/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Actor::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Actor index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'actor[firstname]' => 'Testing',
            'actor[lastname]' => 'Testing',
            'actor[birthDate]' => 'Testing',
            'actor[programs]' => 'Testing',
        ]);

        self::assertResponseRedirects('/actor/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Actor();
        $fixture->setFirstname('My Title');
        $fixture->setLastname('My Title');
        $fixture->setBirthDate('My Title');
        $fixture->setPrograms('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Actor');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Actor();
        $fixture->setFirstname('My Title');
        $fixture->setLastname('My Title');
        $fixture->setBirthDate('My Title');
        $fixture->setPrograms('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'actor[firstname]' => 'Something New',
            'actor[lastname]' => 'Something New',
            'actor[birthDate]' => 'Something New',
            'actor[programs]' => 'Something New',
        ]);

        self::assertResponseRedirects('/actor/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getFirstname());
        self::assertSame('Something New', $fixture[0]->getLastname());
        self::assertSame('Something New', $fixture[0]->getBirthDate());
        self::assertSame('Something New', $fixture[0]->getPrograms());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Actor();
        $fixture->setFirstname('My Title');
        $fixture->setLastname('My Title');
        $fixture->setBirthDate('My Title');
        $fixture->setPrograms('My Title');

        $this->repository->save($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/actor/');
    }
}
