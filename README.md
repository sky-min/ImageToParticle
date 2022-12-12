# Todo
- [ ] roll (need help)
- [x] yaw pitch equal to entity yaw pitch

# how to use
1. this plugin load
2. image file put at plugin_data/ImageParticle/image/
3. Enter a file name at plugin_data/ImageParticle/Images.yml
4. server reboot

[plugin_data example](https://github.com/sky-min/ImageToParticle/tree/master/example)

# API
## registerImageParticle
```php
use skymin\ImageParticle\particle\ImageParticleAPI;

/** @see skymin\ImageParticle\utils\ImageTypes for $imageType*/
ImageParticleAPI::getInstance()->registerImage(string $name, string $imageFile, int $imageType);
```

## sendImageParticle
```php
use skymin\ImageParticle\particle\ImageParticleAPI;

/** @param pocketmine\entity\Location $center */
ImageParticleAPI::getInstance()->sendParticle(string $name, Location $center, int $count, float $unit, bool $asyncEncode);
```
