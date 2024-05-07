<?php

namespace App\Service;

use App\Entity\Follow;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserManager
{
    public function __construct(private readonly EntityManagerInterface $manager)
    {
    }
    
    public function save(User $entity, bool $flush = true): void
    {
        $this->manager->persist($entity);
        
        if ($flush) {
            $this->manager->flush();
        }
    }
    
    public function remove(User $entity, bool $flush = true): void
    {
        $entity
            ->setEmail($entity->getEmail() . '-deleted-' . date('Ymd-His'))
            ->setDeletedAt(new \DateTimeImmutable());
        
        if ($flush) {
            $this->manager->flush();
        }
    }
    
    /**
     * @param User $user
     *
     * @return array<User>
     */
    public function getRecommendedUsers(User $user): array
    {
        $followings = $user->getFollowings();
        
        $level1FollowingUsers = $level2FollowingUsers = [];

        /** @var Follow $level1Following */
        foreach ($followings as $level1Following) {
            $followingUser = $level1Following->getFollowing();
            
            $level1FollowingUsers[$followingUser->getId()] = $followingUser;
            
            /** @var Follow $level2Following */
            foreach ($followingUser->getFollowings() as $level2Following) {
                $level2FollowingUser = $level2Following->getFollowing();
                
                if ($level2FollowingUser->getId() === $user->getId()) {
                    continue;
                }
                
                if (isset($level1FollowingUsers[$level2FollowingUser->getId()])) {
                    continue;
                }
                
                $level2FollowingUsers[$level2FollowingUser->getId()] = $level2FollowingUser;
            }
        }
        
        return $level2FollowingUsers;
    }
}