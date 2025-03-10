<?php

namespace App\Tests\Handler;

use App\Entity\Domain;
use App\Entity\OpenPgpKey;
use App\Entity\User;
use App\Handler\WkdHandler;
use App\Repository\OpenPgpKeyRepository;
use DateTime;
use Doctrine\Common\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;

class WkdHandlerTest extends TestCase
{
    private $openPgpKey;
    private $email = 'alice@example.org';
    private $domain = 'example.org';
    private $wkdDirectory = '/tmp/wkd';
    private $wkdFormat = 'advanced';
    private $keyData = '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';
    private $keyId = 'AD8D52CD2281FEC2';
    private $keyFingerprint = '7301 2547 C25D E2A0 D097  8C46 AD8D 52CD 2281 FEC2';
    private $keyExpireTime = '@1665415900';
    private $wkdHash = 'kei1q4tipxxu1yj79k9kfukdhfy631xe';
    private $wkdPath;

    protected function setUp(): void
    {
        $this->wkdPath = $this->wkdDirectory.DIRECTORY_SEPARATOR.$this->domain.DIRECTORY_SEPARATOR.'hu'.DIRECTORY_SEPARATOR.$this->wkdHash;

        parent::setUp(); // TODO: Change the autogenerated stub
    }

    private function createHandler()
    {
        $this->openPgpKey = new OpenPgpKey();
        $this->openPgpKey->setEmail($this->email);
        $this->openPgpKey->setKeyId($this->keyId);
        $this->openPgpKey->setKeyFingerprint($this->keyFingerprint);
        $this->openPgpKey->setKeyExpireTime(new DateTime($this->keyExpireTime));
        $this->openPgpKey->setKeyData($this->keyData);
        $repository = $this->getMockBuilder(OpenPgpKeyRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $repository->method('findByEmail')->willReturn($this->openPgpKey);

        $manager = $this->getMockBuilder(ObjectManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $manager->method('getRepository')->willReturn($repository);

        return new WkdHandler($manager, $this->wkdDirectory, $this->wkdFormat);
    }

    public function testImportKey(): void
    {
        $expected = new OpenPgpKey();
        $expected->setEmail($this->email);
        $expected->setKeyId($this->keyId);
        $expected->setKeyFingerprint($this->keyFingerprint);
        $expected->setKeyExpireTime(new DateTime($this->keyExpireTime));
        $expected->setKeyData($this->keyData);

        $handler = $this->createHandler();
        $wkdKey = $handler->importKey(base64_decode($this->keyData), $this->email);
        //overwrite timestamps as they may differ by a few microseconds
        $wkdKey->setCreationTime($expected->getCreationTime());
        $wkdKey->setUpdatedTime($expected->getUpdatedTime());

        self::assertEquals($expected, $wkdKey);
        self::assertFileExists($this->wkdPath);
    }

    public function testImportKeyWithUser(): void
    {
        $domain = new Domain();
        $domain->setName(explode('@', $this->email)[1]);
        $user = new User();
        $user->setDomain($domain);
        $user->setEmail($this->email);
        $expected = new OpenPgpKey();
        $expected->setEmail($this->email);
        $expected->setKeyId($this->keyId);
        $expected->setKeyFingerprint($this->keyFingerprint);
        $expected->setKeyExpireTime(new DateTime($this->keyExpireTime));
        $expected->setKeyData($this->keyData);
        $expected->setUser($user);

        $handler = $this->createHandler();
        $wkdKey = $handler->importKey(base64_decode($this->keyData), $this->email, $user);

        //overwrite timestamps as they may differ by a few microseconds
        $wkdKey->setCreationTime($expected->getCreationTime());
        $wkdKey->setUpdatedTime($expected->getUpdatedTime());

        self::assertEquals($expected, $wkdKey);
        self::assertFileExists($this->wkdPath);
    }

    public function testDeleteKey(): void
    {
        if (!is_dir(dirname($this->wkdPath))) {
            mkdir(dirname($this->wkdPath), 0755, true);
        }
        touch($this->wkdPath);

        $handler = $this->createHandler();
        $handler->deleteKey($this->email);

        self::assertFileNotExists($this->wkdPath);
    }

    public function testExportKeyToWKD(): void
    {
        if (!is_dir(dirname($this->wkdPath))) {
            mkdir(dirname($this->wkdPath), 0755, true);
        } elseif (is_file($this->wkdPath)) {
            unlink($this->wkdPath);
        }

        $handler = $this->createHandler();
        $handler->exportKeyToWKD($this->openPgpKey);

        self::assertFileExists($this->wkdPath);
        self::assertEquals(base64_decode($this->keyData), file_get_contents($this->wkdPath));
    }
}
