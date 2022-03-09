<?php

namespace App\DataFixtures;

use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Product;
use Liior\Faker\Prices;
use App\Entity\Category;
use Bezhanov\Faker\Provider\Commerce;
use Bluemmb\Faker\PicsumPhotosProvider;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{
    protected $slugger;
    protected $hasher;

    public function __construct(SluggerInterface $slugger, UserPasswordHasherInterface $hasher)
    {
        $this->slugger = $slugger;
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $faker = Factory::create('fr_FR');
        $faker->addProvider(new Prices($faker));
        $faker->addProvider(new Commerce($faker));
        $faker->addProvider(new PicsumPhotosProvider($faker));


        $admin = new User;
        $hash = $this->hasher->hashPassword($admin, "admin");
        $admin->setEmail("admin@gmail.com")
            ->setFullName("Admin")
            ->setPassword($hash)
            ->setRoles(["ROLE_ADMIN"]);

        $manager->persist($admin);

        $users = [];
        for ($u = 0; $u < 5; $u++) {
            # code...
            $user = new User;
            $hash = $this->hasher->hashPassword($user, "password");
            $user->setEmail("user$u@gmail.com")
                ->setFullName($faker->name)
                ->setPassword($hash);

            $users[] = $user;

            $manager->persist($user);
        }

        $products = [];
        for ($c = 0; $c < 3; $c++) {
            # code...
            $category = new Category;
            $category->setName($faker->department)
                ->setSlug(strtolower($this->slugger->slug($category->getName())));

            $manager->persist($category);

            for ($p = 0; $p < 10; $p++) {
                $product = new Product;
                $product->setName($faker->productName)
                    ->setPrice($faker->price(4000, 20000))
                    ->setSlug(strtolower($this->slugger->slug($product->getName())))
                    ->setCategory($category)
                    ->setShortDescription($faker->paragraph)
                    ->setMainPicture($faker->imageUrl(150, 150, true));

                $products[] = $product;
                $manager->persist($product);
            }
        }
        for($p =0; $p < mt_rand(20, 40); $p++){
            $purchase = new Purchase();

            $purchase->setFullName($faker->name)
                ->setAddress($faker->streetAddress)
                ->setCity($faker->city)
                ->setTotal(mt_rand(2000, 30000))
                ->setPostalCode($faker->postcode)
                ->setUser($faker->randomElement($users))
                ->setPurchasedAt($faker->dateTimeBetween('-6 months', 'now'));


            $selectedProducts = $faker->randomElements($products, mt_rand(3, 5));
            foreach ($selectedProducts as $prod){
                $purchaseItem = new PurchaseItem;
                $purchaseItem->setProduct($prod)
                    ->setQuantity(mt_rand(1, 3))
                    ->setProductName($prod->getName())
                    ->setProductPrice($prod->getPrice())
                    ->setTotal(
                        $purchaseItem->getProductPrice() * $purchaseItem->getQuantity()
                    )
                    ->setPurchase($purchase);
                $manager->persist($purchaseItem);
            }

            if ($faker->boolean(90)){
                $purchase->setStatus(Purchase::STATUS_PAID);
            }

            $manager->persist($purchase);

        }
        $manager->flush();
    }
}
