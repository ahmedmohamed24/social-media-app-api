# Social Media app

## Features:
* Admin
   - create admin (seeding)
   - login
   - logout
   - retrieve admin info
* Client
    - get all clients
    - create
    - update
    - delete
* User
  - register to specific client (send a welcome email and create a **Profile**)
  - login
  - logout
  - retrieve info
  - update profile info (bio,address, etc ..)
* Post
    - create
    - update
    - soft delete
    - paginate on posts
    - show post with related info (likes, comments, and replies)
    - restore
    - force delete
    - paginate on specific user posts
    - cache post result with redis
* Comment
    - create
    - update
    - soft delete
    - restore
    - force delete
* Reply
    - create reply to a comment
    - update
    - soft delete
    - force delete
    - restore
* Like
    - like and dislike a post
    - like and dislike a comment
    - like and dislike a reply
    - return likes and sum of them when viewing (post,likes,comments)
* Users Relations
    - add friend
    - approve friend request
    - reject a friend request
    - get friend requests list (sent and received)
    - list of friends
    - remove a friend
    - block a user
    - list of blocked users
    - unblock a user

