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
                'id' => '893cf1e6-6751-4aa1-b8fe-7a22dad0b4e1',
                'name' => 'Vegetables'
            ],
            (object) [
                'id' => '85334e9f-338b-48f5-af00-782cb383a2d1',
                'name' => 'Fruit'
            ],
            (object) [
                'id' => '95e45a45-0bbd-455d-8575-5a4f00f857ce',
                'name' => 'Salad'
            ],
            (object) [
                'id' => '94f40ad5-e674-4318-a95d-4127be2110fd',
                'name' => 'Milk and Eggs'
            ],
            (object) [
                'id' => '35cd86a8-e3cd-42b7-ae94-7b63e08e6878',
                'name' => 'Fresh Herbs'
            ],
            (object) [
                'id' => 'cae5df51-6707-4281-9f25-16e147b9458d',
                'name' => 'Exotic Produce'
            ],
            (object) [
                'id' => '66c68eef-8ede-4574-9e10-bab7a2115100',
                'name' => 'Mushrooms'
            ],
            (object) [
                'id' => 'fbddcef1-0dcf-4f01-a990-2bab98d60959',
                'name' => 'Potatoes'
            ],
        ];
    }
}
