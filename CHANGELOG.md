# Changelog

All notable changes to this project will be documented in this file. See [standard-version](https://github.com/conventional-changelog/standard-version) for commit guidelines.

### [3.0.1](https://github.com/renanhangai/libweb-api/compare/v3.0.0...v3.0.1) (2019-10-09)


### Bug Fixes

* delete, put get and post now use the DI if available ([3f5ddc1](https://github.com/renanhangai/libweb-api/commit/3f5ddc1))

## [3.0.0](https://github.com/renanhangai/libweb-api/compare/v2.7.2...v3.0.0) (2019-08-20)


### ⚠ BREAKING CHANGES

* the default response has changed

### Bug Fixes

* Format response now returns the full data ([4f4e3fd](https://github.com/renanhangai/libweb-api/commit/4f4e3fd))

### [2.7.2](https://github.com/renanhangai/libweb-api/compare/v2.7.1...v2.7.2) (2019-08-20)


### Bug Fixes

* Status code of response when an error has ocurred ([aec3eb0](https://github.com/renanhangai/libweb-api/commit/aec3eb0))

### [2.7.1](https://github.com/renanhangai/libweb-api/compare/v2.7.0...v2.7.1) (2019-08-16)


### Bug Fixes

* Resolver when using php-di as the container ([87402fa](https://github.com/renanhangai/libweb-api/commit/87402fa))

## [2.7.0](https://github.com/renanhangai/libweb-api/compare/v2.6.0...v2.7.0) (2019-08-16)


### Features

* Added DI container on application ([738e4c4](https://github.com/renanhangai/libweb-api/commit/738e4c4))

<a name="2.6.0"></a>
# [2.6.0](https://github.com/renanhangai/libweb-api/compare/v2.5.1...v2.6.0) (2019-05-09)


### Bug Fixes

* Graphql playground options were not being properly set ([175ed59](https://github.com/renanhangai/libweb-api/commit/175ed59))
* withString response method was not working ([ca200f4](https://github.com/renanhangai/libweb-api/commit/ca200f4))


### Features

* Added graphql-playground ([71c20d4](https://github.com/renanhangai/libweb-api/commit/71c20d4))



<a name="2.5.1"></a>
## [2.5.1](https://github.com/renanhangai/libweb-api/compare/v2.5.0...v2.5.1) (2019-04-25)


### Bug Fixes

* withDownload contentType was not being used ([278719d](https://github.com/renanhangai/libweb-api/commit/278719d))



<a name="2.5.0"></a>
# [2.5.0](https://github.com/renanhangai/libweb-api/compare/v2.4.2...v2.5.0) (2019-04-03)


### Bug Fixes

* Added CORS expose headers ([edf80a2](https://github.com/renanhangai/libweb-api/commit/edf80a2))


### Features

* New withFile e withString methods for response ([3742953](https://github.com/renanhangai/libweb-api/commit/3742953))



<a name="2.4.2"></a>
## [2.4.2](https://github.com/renanhangai/libweb-api/compare/v2.4.1...v2.4.2) (2019-02-08)


### Bug Fixes

* Credentials when using cors ([52b9b1a](https://github.com/renanhangai/libweb-api/commit/52b9b1a))



<a name="2.4.1"></a>
## [2.4.1](https://github.com/renanhangai/libweb-api/compare/v2.4.0...v2.4.1) (2018-12-07)


### Bug Fixes

* Method was not working with stdClass object ([991e8f0](https://github.com/renanhangai/libweb-api/commit/991e8f0))



<a name="2.4.0"></a>
# [2.4.0](https://github.com/renanhangai/libweb-api/compare/v2.3.1...v2.4.0) (2018-12-06)


### Features

* New class request/response definition ([a47fd30](https://github.com/renanhangai/libweb-api/commit/a47fd30))



<a name="2.3.1"></a>
## [2.3.1](https://github.com/renanhangai/libweb-api/compare/v2.3.0...v2.3.1) (2018-12-05)


### Bug Fixes

* Attributes that are functions are now called correcly and cached ([e4f14c2](https://github.com/renanhangai/libweb-api/commit/e4f14c2))



<a name="2.3.0"></a>
# [2.3.0](https://github.com/renanhangai/libweb-api/compare/v2.2.0...v2.3.0) (2018-12-05)


### Features

* Request now can access attributes directly if they exist ([d417083](https://github.com/renanhangai/libweb-api/commit/d417083))



<a name="2.2.0"></a>
# [2.2.0](https://github.com/renanhangai/libweb-api/compare/v2.0.0...v2.2.0) (2018-11-16)


### Features

* Added getValidatedParamsWithUpload on the request ([60abc61](https://github.com/renanhangai/libweb-api/commit/60abc61))
* Added sugar handler of graphql function params ([051d594](https://github.com/renanhangai/libweb-api/commit/051d594))



<a name="2.1.0"></a>
# [2.1.0](https://github.com/renanhangai/libweb-api/compare/v2.0.0...v2.1.0) (2018-10-17)


### Features

* Added getValidatedParamsWithUpload on the request ([60abc61](https://github.com/renanhangai/libweb-api/commit/60abc61))



<a name="2.0.0"></a>
# [2.0.0](https://github.com/renanhangai/libweb-api/compare/v1.6.3...v2.0.0) (2018-09-04)


### Bug Fixes

* Changed config call to static ([0cad5ab](https://github.com/renanhangai/libweb-api/commit/0cad5ab))


### Chores

* Added default handler for errors as parameter ([451aa7c](https://github.com/renanhangai/libweb-api/commit/451aa7c))


### BREAKING CHANGES

* The error handler will now have a new parameter so changing the signature of the method



<a name="1.6.3"></a>
## [1.6.3](https://github.com/renanhangai/libweb-api/compare/v1.6.2...v1.6.3) (2018-09-04)


### Bug Fixes

* Missing parameters from executeQuery options ([dad91e5](https://github.com/renanhangai/libweb-api/commit/dad91e5))



<a name="1.6.2"></a>
## [1.6.2](https://github.com/renanhangai/libweb-api/compare/v1.6.1...v1.6.2) (2018-09-04)


### Bug Fixes

* Fixed path for the cookie ([f6616ef](https://github.com/renanhangai/libweb-api/commit/f6616ef))



<a name="1.6.1"></a>
## [1.6.1](https://github.com/renanhangai/libweb-api/compare/v1.6.0...v1.6.1) (2018-09-03)


### Bug Fixes

* Fixed cookie method on response ([1d3d45e](https://github.com/renanhangai/libweb-api/commit/1d3d45e))



<a name="1.6.0"></a>
# [1.6.0](https://github.com/renanhangai/libweb-api/compare/v1.5.0...v1.6.0) (2018-09-03)



<a name="1.5.0"></a>
# [1.5.0](https://github.com/renanhangai/libweb-api/compare/v1.4.2...v1.5.0) (2018-09-02)



<a name="1.4.2"></a>
## [1.4.2](https://github.com/renanhangai/libweb-api/compare/v1.4.1...v1.4.2) (2018-08-05)


### Bug Fixes

* **graphql:** Parameter when calling graphql options ([d236401](https://github.com/renanhangai/libweb-api/commit/d236401))



<a name="1.4.1"></a>
## [1.4.1](https://github.com/renanhangai/libweb-api/compare/v1.4.0...v1.4.1) (2018-08-05)


### Bug Fixes

* **graphql:** Options now accepts a callback ([eff894a](https://github.com/renanhangai/libweb-api/commit/eff894a))



<a name="1.4.0"></a>
# [1.4.0](https://github.com/renanhangai/libweb-api/compare/v1.3.0...v1.4.0) (2018-07-29)


### Bug Fixes

* Fixed cors notFound handler ([2c4914e](https://github.com/renanhangai/libweb-api/commit/2c4914e))



<a name="1.3.0"></a>
# [1.3.0](https://github.com/renanhangai/libweb-api/compare/v1.2.4...v1.3.0) (2018-07-13)


### Bug Fixes

* Adicionado .gitignore ([cf22a8a](https://github.com/renanhangai/libweb-api/commit/cf22a8a))
* Error handler was not being called for caught exceptions ([101dd33](https://github.com/renanhangai/libweb-api/commit/101dd33))
* Removed version tag from composer ([443d1c2](https://github.com/renanhangai/libweb-api/commit/443d1c2))


### Features

* Adicionado package.json ([d6c35f5](https://github.com/renanhangai/libweb-api/commit/d6c35f5))
* Removed validator dependency and put as suggestion ([32b2a44](https://github.com/renanhangai/libweb-api/commit/32b2a44))



<a name="1.2.5"></a>
## [1.2.5](https://github.com/renanhangai/libweb-api/compare/v1.2.4...v1.2.5) (2018-06-12)


### Bug Fixes

* Error handler was not being called for caught exceptions ([101dd33](https://github.com/renanhangai/libweb-api/commit/101dd33))



<a name="1.2.4"></a>
## [1.2.4](https://github.com/renanhangai/libweb-api/compare/v1.2.3...v1.2.4) (2018-06-12)


### Bug Fixes

* Adicionado .gitignore ([cf22a8a](https://github.com/renanhangai/libweb-api/commit/cf22a8a))


### Features

* Adicionado package.json ([d6c35f5](https://github.com/renanhangai/libweb-api/commit/d6c35f5))
