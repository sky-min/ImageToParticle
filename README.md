# Todo
- [ ] roll (need help)
- [x] yaw pitch equal to entity yaw pitch

# how to use
1. this plugin load
2. image file put at plugin_data/ImageParticle/image/
3. Enter a file name at plugin_data/ImageParticle/Images.txt
4. server reboot

```php
use skymin\ImageParticle\ImageParticleAPI;

ImageParticleAPI::getInstance()->sendParticle(string $name, Position $center, float $yaw, float $pitch, int $count, float $unit, bool $asyncEncode);
```
