<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Installation

This project build with Laravel, run on Sails. So after pull this repo, just run

```
sail up
sail artisan migrate
```

This app don't build in register feature, so I've already created seeding data, pls run

```
sail arisan db:seed
```
It create 2 users: `user1@gmail.com`, `user2@gmail.com`, and admin: `admin1@gmail.com`

## How to use
Project have 6 API. You can import Postman Collection here: https://www.postman.com/collections/79ee912cdafaee49290e

- User login: Used to login to get Bearer token
- User create loan: Create new loan
- User view loans: View what loan user have, and schedule payment of each loan
- User Repay loan: Repay loan
- Admin login: Admin login to get Bearer token
- Admin approve loan: Admin approve loan, so user can repay.




