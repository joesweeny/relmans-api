<?php

namespace Relmans\Application\Console\Command;

use Ramsey\Uuid\Uuid;
use Relmans\Domain\Entity\Category;
use Relmans\Domain\Persistence\CategoryRepository;
use Relmans\Framework\Time\Clock;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CategorySeedCommand extends Command
{
    private CategoryRepository $repository;
    private Clock $clock;

    public function __construct(CategoryRepository $repository, Clock $clock)
    {
        $this->repository = $repository;
        $this->clock = $clock;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $console = new SymfonyStyle($input, $output);

        foreach ($this->categories() as $c) {
            $category = new Category(
                Uuid::fromString($c->id),
                $c->name,
                $this->clock->now(),
                $this->clock->now()
            );

            $this->repository->insert($category);

            $console->info("Category {$c->name} created");
        }

        $console->success('Categories seeded');

        return Command::SUCCESS;
    }

    protected function configure()
    {
        $this->setName('category:seed')
            ->setDescription('Seed category resources');
    }

    /**
     * @return array|object[]
     */
    private function categories(): array
    {
        return [
            (object) [
                'id' => '3570a561-fc51-4ff6-ab7e-474e0c2a1190',
                'name' => 'Fruit'
            ],
            (object) [
                'id' => 'efa199a2-85c0-4d52-a270-0f11c7034cb9',
                'name' => 'Vegetables'
            ],
            (object) [
                'id' => '00f3062b-24d4-476e-8f78-fa41bd0c696a',
                'name' => 'Salad'
            ],
            (object) [
                'id' => 'd6d5655c-d95a-4c2c-bc8a-f39b80ee38c3',
                'name' => 'Potatoes'
            ],
            (object) [
                'id' => '317c4c6b-081a-4fb3-be86-95fdc8b8c337',
                'name' => 'Mushrooms'
            ],
            (object) [
                'id' => '5d15468b-a1cb-447e-bf42-9abc4abaedf1',
                'name' => 'Milk and Eggs'
            ],
            (object) [
                'id' => '10d640b4-b07a-482f-8fec-44047cb6a006',
                'name' => 'Fresh Herbs'
            ],
            (object) [
                'id' => 'f765244e-0905-459a-8994-87239a25d742',
                'name' => 'Exotic Produce'
            ],
        ];
    }
}
