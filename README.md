# Todo
- [ ] roll (need help)

# how to use
1. this plugin load
2. png file put at plugin_data/ImageParticle/image/
3. Enter a file name excluding the file extension name at plugin_data/ImageParticle/Images.txt
4. server reboot
```php
use skymin\ImageParticle\ImageParticleAPI;

ImageParticleAPI::getInstance()->sendParticle(string $name, Position $center, float $yaw, float $pitch, int $count, float $unit, $look, bool $asyncEncode);
```
