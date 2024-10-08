<?php

namespace Crud;

use Entity\Exception\EntityNotFoundException;
use Entity\Tvshow;
use Tests\CrudTester;

class TvshowCest
{
    public function findById(CrudTester $I): void
    {
        $show = Tvshow::findById(3);
        $I->assertSame(3, $show->getId());
        $I->assertSame('Friends', $show->getName());
        $show = Tvshow::findById(25);
        $I->assertSame(25, $show->getId());
        $I->assertSame('Futurama', $show->getOriginalName());
    }

    public function findByIdThrowsExceptionIfTvshowDoesNotExist(CrudTester $I): void
    {
        $I->expectThrowable(EntityNotFoundException::class, function () {
            Tvshow::findById(PHP_INT_MAX);
        });
    }

    public function getPoster(CrudTester $I): void
    {
        $show = Tvshow::findById(3);
        $I->assertSame("http://localhost:8000/poster.php?posterId=15", $show->getPoster());

    }

    public function delete(CrudTester $I): void
    {
        $show = Tvshow::findById(70);
        $show->delete();
        $I->cantSeeInDatabase('tvshow', ['id' => 70]);
        $I->cantSeeInDatabase('tvshow', ['name' => 'Hunters']);
        $I->assertNull($show->getId());
        $I->assertSame('Hunters', $show->getName());
    }

    public function update(CrudTester $I): void
    {
        $show = Tvshow::findById(57);
        $show->setName('name test');
        $show->setOriginalName('OG name test');
        $show->setHomepage('HP test');
        $show->setOverview('OV test');
        $show->save();
        $I->canSeeNumRecords(1, 'tvshow', [
            'id' => 57,
            'name' => 'name test',
            'originalName' => 'OG name test',
            'homepage' => 'HP test',
            'overview' => 'OV test'
        ]);
        $I->assertSame(57, $show->getId());
        $I->assertSame('name test', $show->getName());
        $I->assertSame('OG name test', $show->getOriginalName());
        $I->assertSame('HP test', $show->getHomepage());
        $I->assertSame('OV test', $show->getOverview());

    }

    public function createWithoutId(CrudTester $I): void
    {
        $show = Tvshow::create('name test', 'OG name test', 'HP test', 'OV test');
        $I->assertNull($show->getId());
        $I->assertSame('name test', $show->getName());
        $I->assertSame('OG name test', $show->getOriginalName());
        $I->assertSame('HP test', $show->getHomepage());
        $I->assertSame('OV test', $show->getOverview());
        $I->assertNull($show->getPosterId());
    }

    public function createWithId(CrudTester $I): void
    {
        $show = Tvshow::create('name test', 'OG name test', 'HP test', 'OV test', 20);
        $I->assertSame(20, $show->getId());
        $I->assertSame('name test', $show->getName());
        $I->assertSame('OG name test', $show->getOriginalName());
        $I->assertSame('HP test', $show->getHomepage());
        $I->assertSame('OV test', $show->getOverview());
        $I->assertNull($show->getPosterId());
    }

    /**
     * @after createWithoutId
     */
    public function insert(Crudtester $I): void
    {
        $show = Tvshow::create('name test', 'OG name test', 'HP test', 'OV test');
        $show->save();
        $I->canSeeNumRecords(1, 'tvshow', [
            'id' => 83,
            'name' => 'name test',
            'originalName' => 'OG name test',
            'homepage' => 'HP test',
            'overview' => 'OV test'
        ]);
        $I->assertSame(83, $show->getId());
        $show->delete();
    }

}
