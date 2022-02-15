<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Genre;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\Game;
use DateInterval;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    const NB_OF_USERS_CREATED = 200; // +3 utilisateurs créé manuellement
    private  int $nbDummyUsers;
    private array $arrayDummyUsers = array();

    const NB_OF_GENRES_CREATED = 50; // +2 genres créé manuellement
    private  int $nbDummyGenres;
    private array $arrayDummyGenres = array();

    const NB_OF_GAMES_CREATED = 100; // +2 jeux créé manuellement
    private int $nbDummyGames;
    private array $arrayDummyGames = array();

    const NB_OF_POSTS_CREATED = 2000; // +2 posts créé manuellement
    private int $nbDummyPosts;
    private array $arrayDummyPosts = array();

    const NB_OF_COMMENTS_CREATED = 3000; // +2 commentaires créé manuellement
    private int $nbDummyComments;
    private array $arrayDummyComments = array();

    public function load(ObjectManager $manager): void
    {
        foreach(glob('public/picture/game/*') as $file){
            if(is_file($file)) {
                unlink($file);
            }
        }
        foreach(glob('public/picture/post/*') as $file){
            if(is_file($file)) {
                unlink($file);
            }
        }
        foreach(glob('public/picture/user/*') as $file){
            if(is_file($file)) {
                unlink($file);
            }
        }

        $faker = \Faker\Factory::create();
        \Bezhanov\Faker\ProviderCollectionHelper::addAllProvidersTo($faker);


        //Users

        $user_jean = new User();
        $user_jean
            ->setEmail("jean@gmail.com")
            ->setPassword('$2y$13$C99P3WTydu0aoXcQ1NkHEuQTTe31CPx/5SqpV6rbH08p/4ia0XG4S')
            ->setUsername("jean1234")
            ->setPicturePath('fixture-asset/user/jean1234.jpg')
        ;
        $manager->persist($user_jean);

        $user_alex = new User();
        $user_alex
            ->setEmail("alex@gmail.com")
            ->setPassword('$2y$13$Cgvvg5MBeozeTx2FprHU6eIqMs6hzk2fzGUpjpFijf0QfktQgVhTi')
            ->setUsername("00alex00")
            ->setPicturePath("fixture-asset/user/00alex00.jpg")
        ;
        $manager->persist($user_alex);

        $user_sacha = new User();
        $user_sacha
            ->setRoles(['ROLE_ADMIN'])
            ->setEmail("sacha@gmail.com")
            ->setPassword('$2y$13$WoPEZ1Cj39NaIfheipx8ROeYidoiLJfSj3d7tH2T7I8eX2Tg8KMZe')
            ->setUsername("sachazerty")
            ->setPicturePath("fixture-asset/user/sachazerty.jpg")
        ;
        $manager->persist($user_sacha);

        //Dummy Users
        for ($i = 0; $i < self::NB_OF_USERS_CREATED; $i++) {
            $dummy_user = new User();
            $dummy_user->setUsername($faker->userName())
                ->setEmail($dummy_user->GetUserName() . "@gmail.com")
                ->setPicturePath("fixture-asset/dummy-user/dummy-user-picture-".rand(0,200).".png")
                ->setPassword('$2y$13$C99P3WTydu0aoXcQ1NkHEuQTTe31CPx/5SqpV6rbH08p/4ia0XG4S')
                ->setRegisteredAt(date_create_immutable()->sub(new DateInterval('P'.rand(10,1000).'D')))
                ;
            $manager->persist($dummy_user);
            $arrayDummyUsers[$i] = $dummy_user;
            $this->nbDummyUsers = $i;
        }

        //Genres

        $genre_mmorpg = new Genre();
        $genre_mmorpg->setLabel("mmorpg");
        $manager->persist($genre_mmorpg);

        $genre_sandbox = new Genre();
        $genre_sandbox->setLabel("sandbox");
        $manager->persist($genre_sandbox);

        //Dummy Genres

        for ($i = 0; $i < self::NB_OF_GENRES_CREATED; $i++) {
            $dummy_genre = new Genre();
            $dummy_genre->SetLabel($faker->word());
            $manager->persist($dummy_genre);
            $arrayDummyGenres[$i] = $dummy_genre;
            $this->nbDummyGenres = $i;
        }

        //Games

        $game_ff14 = new Game();
        $game_ff14->setAuthor($user_jean)
            ->setTitle("ff14")
            ->setPicturePath("fixture-asset/game/ff14.jpg")
            ->setReleaseDate(date_create_immutable())
            ->addGenre($genre_mmorpg);
        $author_ff14 = $game_ff14->getAuthor();
        $game_ff14->addUser($author_ff14);
        $manager->persist($game_ff14);

        $game_minecraft = new Game();
        $game_minecraft->setAuthor($user_alex)
            ->setTitle("minecraft")
            ->setPicturePath("fixture-asset/game/minecraft.jpg")
            ->setReleaseDate(date_create_immutable())
            ->addGenre($genre_sandbox);
        $author_minecraft = $game_minecraft->getAuthor();
        $game_minecraft->addUser($author_minecraft);
        $manager->persist($game_minecraft);

        //Dummy Games

        for ($i = 0; $i < self::NB_OF_GAMES_CREATED; $i++) {
            $dummy_game = new Game();
            $dummy_game->setAuthor($arrayDummyUsers[rand(0, $this->nbDummyUsers)])
                ->setTitle('G_' . $faker->word())
                ->setPicturePath("fixture-asset/dummy-game-picture.png")
                ->setReleaseDate($faker->dateTime)
                ->addGenre($arrayDummyGenres[rand(0, $this->nbDummyGenres)]);
            $author_dummy_game = $dummy_game->getAuthor();
            $dummy_game->addUser($author_dummy_game);
            $manager->persist($dummy_game);
            $arrayDummyGames[$i] = $dummy_game;
            $this->nbDummyGames = $i;
        }

        //Game Affectations

        $game_ff14->addUser($user_alex);
        $manager->persist($game_ff14);

        $game_minecraft->addUser($user_jean);
        $manager->persist($game_minecraft);

        //Dummy Game Affectations

        for ($i=0;$i<100;$i++){
            $arrayDummyGames[rand(0, $this->nbDummyGames)]->AddUser($arrayDummyUsers[rand(0, $this->nbDummyUsers)]);
        }

        //Posts

        $post_1 = new Post();
        $post_1->setAuthor($user_jean)
            ->setGame($game_minecraft)
            ->setTitle("Très beau jeu")
            ->setDescription("")
            ->setPicturePath("fixture-asset/post/Très beau jeu.jpg");
        $manager->persist($post_1);

        $post_2 = new Post();
        $post_2->setAuthor($user_alex)
            ->setGame($game_ff14)
            ->setTitle("Gameplay incroyable")
            ->setDescription("desc")
            ->setPicturePath("fixture-asset/post/Gameplay incroyable.jpg");
        $manager->persist($post_2);

        //Dummy Posts

        for ($i = 0; $i < self::NB_OF_POSTS_CREATED; $i++) {
            $dummy_post = new post();
            $dummy_post->setAuthor($arrayDummyUsers[rand(0, $this->nbDummyUsers)])
                ->setGame($arrayDummyGames[rand(0, $this->nbDummyGames)])
                ->setTitle('P_' . $faker->word())
                ->setDescription($faker->sentence(10))
                ->setCreatedAt(date_create_immutable()->sub(new DateInterval('P'.rand(10,1000).'D')))
                ->setPicturePath("fixture-asset/dummy-post/dummy-post-picture-".rand(0,200).".png");
            $manager->persist($dummy_post);
            $arrayDummyPosts[$i] = $dummy_post;
            $this->nbDummyPosts = $i;
        }

        //Comments

        $comment_1 = new Comment();
        $comment_1->setAuthor($user_jean)
            ->setPost($post_2)
            ->setContent("yes");
        $manager->persist($comment_1);

        $comment_2 = new Comment();
        $comment_2->setAuthor($user_alex)
            ->setPost($post_1)
            ->setContent("no");
        $manager->persist($comment_2);

        //Dummy Comments

        for ($i = 0; $i < self::NB_OF_COMMENTS_CREATED; $i++) {
            $dummy_comment = new Comment();
            $dummy_comment->setAuthor($arrayDummyUsers[rand(0, $this->nbDummyUsers)])
                ->setPost($arrayDummyPosts[rand(0, $this->nbDummyPosts)])
                ->setContent($faker->sentence(10));
            $manager->persist($dummy_comment);
            $arrayDummyComments[$i] = $dummy_comment;
            $this->nbDummyComments = $i;
        }

        //Flush

        $manager->flush();
    }
}
