<?php

namespace Relmans\Application\Console\Command;

use Ramsey\Uuid\Uuid;
use Relmans\Domain\Entity\Product;
use Relmans\Domain\Entity\ProductPrice;
use Relmans\Domain\Enum\Measurement;
use Relmans\Domain\Enum\ProductStatus;
use Relmans\Domain\Persistence\ProductWriter;
use Relmans\Framework\Time\Clock;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ProductSeedCommand extends Command
{
    private ProductWriter $productWriter;
    private Clock $clock;

    public function __construct(ProductWriter $productWriter, Clock $clock)
    {
        $this->productWriter = $productWriter;
        $this->clock = $clock;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $console = new SymfonyStyle($input, $output);

        foreach ($this->products() as $p) {
            $productId = Uuid::fromString($p->id);

            $prices = array_map(function (object $price) use ($productId) {
                return new ProductPrice(
                    Uuid::fromString($price->id),
                    $productId,
                    $price->value,
                    $price->size,
                    new Measurement($price->measurement),
                    $this->clock->now(),
                    $this->clock->now()
                );
            }, $p->prices);

            $category = new Product(
                $productId,
                Uuid::fromString($p->categoryId),
                $p->name,
                new ProductStatus($p->status),
                $p->featured,
                $prices,
                $this->clock->now(),
                $this->clock->now()
            );

            $this->productWriter->insert($category);

            $console->info("Product {$p->name} created");
        }

        $console->success('Products seeded');

        return Command::SUCCESS;
    }

    protected function configure()
    {
        $this->setName('product:seed')
            ->setDescription('Seed product resources');
    }

    /**
     * @return array|object[]
     */
    private function products(): array
    {
        return [
            (object) [
                'id' => '1d38394c-02d2-4688-98f4-58e8c33ba952',
                'name' => 'Cauliflower',
                'categoryId' => 'efa199a2-85c0-4d52-a270-0f11c7034cb9',
                'status' => 'IN_STOCK',
                'featured' => true,
                'prices' => [
                    (object) [
                        'id' => '7ef146d2-db7f-41c3-a9aa-bfae8306a3d2',
                        'value' => 200,
                        'size' => 1,
                        'measurement' => 'EACH',
                    ],
                    (object) [
                        'id' => '7b607394-ceda-4ee4-8762-0c85841c7a95',
                        'value' => 150,
                        'size' => 1,
                        'measurement' => 'HALF',
                    ],
                ]
            ],
            (object) [
                'id' => '5e4988d6-09f3-4d30-8be9-1006aa50ff53',
                'name' => 'Golden Delicious Apples',
                'categoryId' => '3570a561-fc51-4ff6-ab7e-474e0c2a1190',
                'status' => 'IN_STOCK',
                'featured' => true,
                'prices' => [
                    (object) [
                        'id' => '29b0587f-8df5-496f-a60f-a7f25a97225d',
                        'value' => 100,
                        'size' => 1,
                        'measurement' => 'EACH',
                    ],
                ],
            ],
            (object) [
                'id' => '71d582f9-cf71-48b6-bed1-30e0cf46823b',
                'name' => 'Cucumber',
                'categoryId' => '00f3062b-24d4-476e-8f78-fa41bd0c696a',
                'status' => 'OUT_OF_STOCK',
                'featured' => false,
                'prices' => [
                    (object) [
                        'id' => '2beee823-b304-4d37-b8cc-5565f2ff94d0',
                        'value' => 100,
                        'size' => 1,
                        'measurement' => 'EACH',
                    ],
                    (object) [
                        'id' => 'c03e8e6b-9b80-4b97-bd38-51068900d911',
                        'value' => 50,
                        'size' => 1,
                        'measurement' => 'HALF',
                    ],
                ],
            ],
            (object) [
                'id' => 'a350a799-8802-41f9-b8a0-89ab62ccb9e4',
                'name' => 'Maris Piper',
                'categoryId' => 'd6d5655c-d95a-4c2c-bc8a-f39b80ee38c3',
                'status' => 'IN_STOCK',
                'featured' => false,
                'prices' => [
                    (object) [
                        'id' => 'a28127ec-5c9e-4a9e-8a89-3747630a28b1',
                        'value' => 25,
                        'size' => 1,
                        'measurement' => 'KILOGRAMS',
                    ],
                    (object) [
                        'id' => 'da0e96e1-6b33-4e44-8e20-23f243663cae',
                        'value' => 200,
                        'size' => 1,
                        'measurement' => 'EACH',
                    ],
                ],
            ],
            (object) [
                'id' => '7b45e57b-5f20-4555-9c62-3e9db3da6d44',
                'name' => 'King Edward',
                'categoryId' => 'd6d5655c-d95a-4c2c-bc8a-f39b80ee38c3',
                'status' => 'OUT_OF_SEASON',
                'featured' => true,
                'prices' => [
                    (object) [
                        'id' => 'bb52a495-f0b3-462d-b4cc-9994fcc40dba',
                        'value' => 25,
                        'size' => 1,
                        'measurement' => 'KILOGRAMS',
                    ],
                    (object) [
                        'id' => '2f979bf2-acf2-44d2-ac88-360823523f3a',
                        'value' => 200,
                        'size' => 1,
                        'measurement' => 'EACH',
                    ],
                ],
            ],
            (object) [
                'id' => '9e374fbb-58e0-44f1-8eda-da03c877f99a',
                'name' => 'Marijuana',
                'categoryId' => '10d640b4-b07a-482f-8fec-44047cb6a006',
                'status' => 'OUT_OF_SEASON',
                'featured' => false,
                'prices' => [
                    (object) [
                        'id' => '1e2bbdd0-b3e8-45cc-bdaf-fecd21c4e23b',
                        'value' => 200,
                        'size' => 1,
                        'measurement' => 'GRAMS',
                    ],
                ],
            ],
            (object) [
                'id' => '231ff439-98fc-4756-8810-1d9187086072',
                'name' => 'Flat Mushrooms',
                'categoryId' => '317c4c6b-081a-4fb3-be86-95fdc8b8c337',
                'status' => 'IN_STOCK',
                'featured' => false,
                'prices' => [
                    (object) [
                        'id' => 'f34e4cd4-1bd9-4607-b95d-296f5a0b85a6',
                        'value' => 100,
                        'size' => 500,
                        'measurement' => 'GRAMS',
                    ],
                ],
            ],
        ];
    }
}
