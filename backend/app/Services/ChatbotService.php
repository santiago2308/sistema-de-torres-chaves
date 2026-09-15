<?php

namespace App\Services;

use App\Models\Service;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ChatbotService
{
    public function respond(string $message, ?Service $contextService = null): array
    {
        $text = $this->normalize($message);

        $specificService = $this->detectService($text);

        $intents = [
            'greeting' => $this->matchAny($text, ['hola', 'buenas', 'buenos dias', 'buenas tardes', 'buenas noches', 'saludos', 'hey', 'que tal']),
            'services' => $this->matchAny($text, ['servicio', 'servicios', 'que tienen', 'que ofrecen', 'catalogo', 'catálogo', 'ofrecen', 'alquiler', 'renta']),
            'prices' => $this->matchAny($text, ['precio', 'precios', 'cuanto cuesta', 'cuanto vale', 'tarifas', 'valor', 'costo', 'cuanto']),
            'category' => $this->detectCategory($text),
            'whatsapp' => $this->matchAny($text, ['whatsapp', 'whats', 'contacto', 'contactar', 'asesor', 'hablar con alguien', 'persona', 'dudas']),
            'location' => $this->matchAny($text, ['donde estan', 'ubicacion', 'direccion', 'donde quedan', 'ciudad', 'zona']),
            'booking' => $this->matchAny($text, ['reservar', 'reserva', 'cotizar', 'cotizacion', 'agendar', 'apartar', 'presupuesto', 'disponibilidad']),
            'schedule' => $this->matchAny($text, ['horario', 'horarios', 'atienden', 'abierto', 'hora de atencion']),
            'thanks' => $this->matchAny($text, ['gracias', 'muchas gracias', 'te agradezco']),
            'bye' => $this->matchAny($text, ['chao', 'adios', 'hasta luego', 'nos vemos']),
        ];

        if ($intents['greeting']) {
            return $this->reply(
                '¡Hola! 👋 Bienvenido/a a nuestro catálogo de servicios para eventos. '
                .'Puedo ayudarte a conocer nuestros servicios y precios. '
                .'Pregúntame, por ejemplo: "¿qué servicios tienen?", "¿cuánto cuesta un sonido básico?" o "¿cómo reservo?"'
            );
        }

        if ($intents['whatsapp']) {
            return $this->withWhatsApp(
                '¡Claro! Para atención personalizada puedes escribirnos directamente por WhatsApp. '
                .'Un asesor te responderá lo más pronto posible. 👇',
            );
        }

        if ($intents['location']) {
            return $this->withWhatsApp(
                'Trabajamos en la cobertura de eventos de la zona. Para confirmar disponibilidad y '
                .'si cubrimos tu ubicación, el mejor canal es WhatsApp. 👇',
            );
        }

        if ($intents['schedule']) {
            return $this->withWhatsApp(
                'Los horarios de atención y agenda de eventos pueden variar según la temporada. '
                .'Escríbenos por WhatsApp para confirmar la disponibilidad según la fecha de tu evento. 👇',
            );
        }

        if ($intents['booking']) {
            return $this->withWhatsApp(
                '¡Excelente decisión! 🎉 Para reservar o cotizar tu evento, escríbenos por WhatsApp '
                .'indicando fecha, tipo de evento y servicios que necesitas. Te confirmamos disponibilidad y precio final. 👇',
            );
        }

        if ($intents['prices'] || $intents['services'] || $intents['category'] || $contextService || $specificService) {
            return $this->catalogReply($text, $intents['category'], $contextService ?? $specificService);
        }

        if ($intents['thanks']) {
            return $this->reply('¡Con gusto! 😊 Si tienes más preguntas estoy aquí para ayudarte.');
        }

        if ($intents['bye']) {
            return $this->withWhatsApp(
                '¡Hasta luego! 👋 Si decidiste contratar alguno de nuestros servicios, escríbenos por WhatsApp. 👇',
            );
        }

        return $this->withWhatsApp(
            'No estoy seguro de haber entendido tu pregunta. 🤔 Puedo ayudarte con: servicios, precios, reservas o ubicación. '
            .'También puedes hablar con un asesor por WhatsApp. 👇',
        );
    }

    private function catalogReply(string $text, ?string $category, ?Service $contextService): array
    {
        if ($contextService) {
            return $this->withWhatsApp(
                "El servicio *{$contextService->name}* tiene un valor de *\${$contextService->price_formatted}*.\n"
                ."\n¿Qué incluye?\n_{$contextService->description}_\n"
                ."\n¿Quieres reservarlo o necesitas más información? 👇"
            );
        }

        $services = $this->cachedServices();
        $filtered = $category ? $services->where('category', $category) : $services;

        if ($filtered->isEmpty()) {
            return $this->reply('No encontré servicios en esa categoría. Puedes ver todas las opciones en el catálogo de esta página. 😊');
        }

        $what = $category ?? 'nuestros servicios';
        $lines = $filtered->map(function (Service $s) {
            return "- *{$s->name}* — \${$s->price_formatted}";
        });

        $text = "Estos son {$what} y sus precios:\n\n"
            .$lines->implode("\n")
            ."\n\n¿Quieres saber qué incluye alguno o reservar? Pregúntame el nombre del servicio o escríbenos por WhatsApp. 👇";

        return $this->withWhatsApp($text);
    }

    private function matchAny(string $text, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (str_contains($text, $keyword)) {
                return true;
            }
        }

        return false;
    }

    private function detectCategory(string $text): ?string
    {
        $map = [
            'Sonido' => ['sonido', 'parlante', 'parlantes', 'bafle', 'bafles', 'microfono', 'microfonos'],
            'Iluminación' => ['iluminacion', 'iluminacion', 'luz', 'luces', 'led'],
            'DJ' => ['dj'],
            'Pantalla LED' => ['pantalla', 'pantallas', 'led'],
            'Efectos' => ['efectos', 'humo', 'laser', 'burbujas'],
            'Packs' => ['pack', 'paquete', 'completo', 'premium', 'fiesta'],
        ];

        foreach ($map as $category => $keywords) {
            if ($this->matchAny($text, $keywords)) {
                return $category;
            }
        }

        return null;
    }

    private function detectService(string $text): ?Service
    {
        if ($text === '' || mb_strlen($text) < 3) {
            return null;
        }

        return $this->cachedServices()
            ->filter(fn (Service $service) => str_contains($text, $this->normalize($service->name)))
            ->first();
    }

    private function cachedServices(): Collection
    {
        return Cache::remember('chatbot:services', now()->addMinutes(10), function () {
            return Service::query()->active()->get();
        });
    }

    private function normalize(string $message): string
    {
        return strtolower(trim(preg_replace('/\s+/', ' ', $message) ?? ''));
    }

    private function reply(string $message): array
    {
        return [
            'message' => $message,
            'show_whatsapp' => false,
        ];
    }

    private function withWhatsApp(string $message): array
    {
        return [
            'message' => $message,
            'show_whatsapp' => true,
        ];
    }
}