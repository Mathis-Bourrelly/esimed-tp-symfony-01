<?php

namespace App\Test\Controller;

use App\Entity\AdminUser;
use App\Repository\AdminUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminUserControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private AdminUserRepository $repository;
    private string $path = '/admin/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(AdminUser::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Admin index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'admin[username]' => 'jean',
            'admin[email]' => 'jean@test.fr',
            'admin[password]' => 'jean',
        ]);

        self::assertResponseRedirects('/admin/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $fixture = new AdminUser();
        $fixture->setUsername('jean');
        $fixture->setEmail('jean@test.fr');
        $fixture->setPassword('jean');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Admin');
    }

    public function testEdit(): void
    {
        $fixture = new AdminUser();
        $fixture->setUsername('jean');
        $fixture->setEmail('jean@test.fr');
        $fixture->setPassword('jean');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'admin[username]' => 'jack',
            'admin[email]' => 'jack@test.fr',
            'admin[password]' => 'jack',
        ]);

        self::assertResponseRedirects('/admin/');

        $fixture = $this->repository->findAll();

        self::assertSame('jack', $fixture[0]->getUsername());
        self::assertSame('jack@test.fr', $fixture[0]->getEmail());
        self::assertEquals(60, strlen($fixture[0]->getPassword()));
    }


    public function testRemove(): void
    {

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new AdminUser();
        $fixture->setUsername('jean');
        $fixture->setEmail('jean@test.fr');
        $fixture->setPassword('jean');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/admin/');
    }
}
