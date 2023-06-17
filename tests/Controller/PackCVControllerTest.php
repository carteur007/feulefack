<?php

namespace App\Test\Controller;

use App\Entity\PackCV;
use App\Repository\PackCVRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PackCVControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private PackCVRepository $repository;
    private string $path = '/pack/c/v/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(PackCV::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('PackCV index');

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
            'pack_c_v[intitule]' => 'Testing',
            'pack_c_v[prix]' => 'Testing',
            'pack_c_v[employeurs]' => 'Testing',
        ]);

        self::assertResponseRedirects('/pack/c/v/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new PackCV();
        $fixture->setIntitule('My Title');
        $fixture->setPrix('My Title');
        $fixture->setEmployeurs('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('PackCV');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new PackCV();
        $fixture->setIntitule('My Title');
        $fixture->setPrix('My Title');
        $fixture->setEmployeurs('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'pack_c_v[intitule]' => 'Something New',
            'pack_c_v[prix]' => 'Something New',
            'pack_c_v[employeurs]' => 'Something New',
        ]);

        self::assertResponseRedirects('/pack/c/v/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getIntitule());
        self::assertSame('Something New', $fixture[0]->getPrix());
        self::assertSame('Something New', $fixture[0]->getEmployeurs());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new PackCV();
        $fixture->setIntitule('My Title');
        $fixture->setPrix('My Title');
        $fixture->setEmployeurs('My Title');

        $this->repository->save($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/pack/c/v/');
    }
}
