# Lang Module
Zajištuje jazykové mutace webu - překlady pro frontend.

## Příklady použití
**PHP Komponenta:**

```
// $this->translate - Wakers\LangModule\Translator\Translate (načteno z DI)
$this->translate->translate('Your name is: %name%', ['name' => $name]);
```

**Latte Šablona:**
```
{translate 'Your name is: %name%', ['name' => $name]}
```

## Console
**Přidání jazyka**
```
$ console wakers:lang-create <lang>
```

## Komponenty

1. `Frontend\SyystemModal` - Výpis editovatelných (systémových) překladů překladů.