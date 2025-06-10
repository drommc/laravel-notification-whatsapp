# Parameter Name Implementation Summary

## Overview

This document summarizes the implementation of `parameter_name` functionality for the Laravel WhatsApp notification package, enabling compliance with Meta WhatsApp Cloud API requirements for named template parameters.

## Meta WhatsApp API Requirements

According to the [Meta WhatsApp Cloud API documentation](https://developers.facebook.com/docs/whatsapp/cloud-api/guides/send-message-templates), template components can include a `parameter_name` field as an alternative to positional parameters. This allows for more maintainable and error-resistant template management.

### API Structure Example
```json
{
  "type": "text",
  "text": "John Doe",
  "parameter_name": "customer_name"
}
```

## Implementation Details

### 1. Core Architecture Changes

#### Enhanced Base Component Class
- **File**: `src/Component/Component.php`
- **Changes**:
  - Added `$parameterName` property
  - Added `parameterName(string $name)` method for fluent interface
  - Added `buildParameterArray(array $baseArray)` helper method
  - Maintains backwards compatibility

```php
abstract class Component
{
    protected ?string $parameterName = null;

    public function parameterName(string $name): self
    {
        $this->parameterName = $name;
        return $this;
    }

    protected function buildParameterArray(array $baseArray): array
    {
        if ($this->parameterName !== null) {
            $baseArray['parameter_name'] = $this->parameterName;
        }
        return $baseArray;
    }

    abstract public function toArray(): array;
}
```

### 2. Component Updates

All existing components were updated to support parameter names:

#### Text Component (`src/Component/Text.php`)
- Modified `toArray()` to use `buildParameterArray()`
- Supports named parameters for text values

#### Currency Component (`src/Component/Currency.php`)
- Enhanced to support parameter names for currency values
- Maintains ISO 4217 currency code support

#### DateTime Component (`src/Component/DateTime.php`)
- Added parameter name support for date/time values
- Preserves custom date formatting

#### Media Components
- **Image** (`src/Component/Image.php`)
- **Video** (`src/Component/Video.php`)
- **Document** (`src/Component/Document.php`)
- All updated to support parameter names for media references

#### Button Component (`src/Component/Button.php`)
- Enhanced base Button class with parameter name support
- Inherited by QuickReplyButton, UrlButton, and FlowButton

### 3. New Location Component

#### Implementation (`src/Component/Location.php`)
- Supports required latitude/longitude coordinates
- Optional name and address fields
- Full parameter name integration
- Complies with Meta WhatsApp location header requirements

```php
public function __construct(float $latitude, float $longitude, ?string $name = null, ?string $address = null)
{
    $this->latitude = $latitude;
    $this->longitude = $longitude;
    $this->name = $name;
    $this->address = $address;
}
```

#### Factory Method (`src/Component.php`)
```php
public static function location(float $latitude, float $longitude, ?string $name = null, ?string $address = null): Component\Location
{
    return new Component\Location($latitude, $longitude, $name, $address);
}
```

## Testing Implementation

### Test Coverage Statistics
- **Total Tests**: 47 tests, 78 assertions
- **Parameter Name Tests**: 9 tests
- **Integration Tests**: 2 tests  
- **Location Component Tests**: 4 tests
- **All Tests Pass**: ✅

### Test Files Created

#### 1. Parameter Name Unit Tests (`tests/Component/ParameterNameTest.php`)
- Tests each component type with parameter names
- Verifies fluent interface functionality
- Ensures backwards compatibility

#### 2. Integration Tests (`tests/ParameterNameIntegrationTest.php`)
- Tests complete template scenarios
- Verifies mixed named/positional parameter usage
- Validates component structure in templates

#### 3. Location Component Tests (`tests/Component/LocationTest.php`)
- Tests basic coordinate functionality
- Tests optional name/address fields
- Verifies parameter name integration

## Usage Examples

### Named Parameters
```php
return WhatsAppTemplate::create()
    ->name('order_confirmation')
    ->header(Component::image('https://example.com/product.jpg')->parameterName('product_image'))
    ->body(Component::text('John Doe')->parameterName('customer_name'))
    ->body(Component::currency(99.99, 'USD')->parameterName('order_total'))
    ->body(Component::dateTime(new \DateTimeImmutable())->parameterName('delivery_date'))
    ->buttons(Component::quickReplyButton(['Track Order'])->parameterName('action_buttons'))
    ->to('1234567890');
```

### Mixed Usage (Named + Positional)
```php
return WhatsAppTemplate::create()
    ->name('mixed_template')
    ->body(Component::text('Welcome'))  // Positional
    ->body(Component::text('John Doe')->parameterName('customer_name'))  // Named
    ->body(Component::currency(50.00, 'EUR'))  // Positional
    ->to('1234567890');
```

### Location Component Usage
```php
return WhatsAppTemplate::create()
    ->header(
        Component::location(37.483307, -122.148981, 'Delivery Location', '1 Main St')
            ->parameterName('delivery_address')
    )
    ->to('1234567890');
```

## API Compliance

### Supported Component Types
All components now support parameter names according to Meta WhatsApp API:

| Component | Header | Body | Button | Parameter Name Support |
|-----------|--------|------|--------|----------------------|
| Text | ✅ | ✅ | ❌ | ✅ |
| Currency | ✅ | ✅ | ❌ | ✅ |
| DateTime | ✅ | ✅ | ❌ | ✅ |
| Image | ✅ | ❌ | ❌ | ✅ |
| Video | ✅ | ❌ | ❌ | ✅ |
| Document | ✅ | ❌ | ❌ | ✅ |
| Location | ✅ | ✅ | ❌ | ✅ |
| Quick Reply | ❌ | ❌ | ✅ | ✅ |
| URL Button | ❌ | ❌ | ✅ | ✅ |
| Flow Button | ❌ | ❌ | ✅ | ✅ |

### Generated API Structure
When using parameter names, components generate the correct API structure:

```json
{
  "messaging_product": "whatsapp",
  "recipient_type": "individual",
  "to": "PHONE_NUMBER",
  "type": "template",
  "template": {
    "name": "TEMPLATE_NAME",
    "language": {
      "code": "LANGUAGE_CODE"
    },
    "components": [
      {
        "type": "header",
        "parameters": [
          {
            "type": "image",
            "image": {
              "link": "https://example.com/image.jpg"
            },
            "parameter_name": "product_image"
          }
        ]
      },
      {
        "type": "body",
        "parameters": [
          {
            "type": "text",
            "text": "John Doe",
            "parameter_name": "customer_name"
          }
        ]
      }
    ]
  }
}
```

## Documentation Updates

### README.md Enhancements
- Added comprehensive parameter name section
- Updated component factory documentation
- Added Location component to supported components list
- Provided practical usage examples

### Code Examples
- Created `examples/ParameterNameUsageExamples.php` with real-world scenarios
- Demonstrates both simple and complex template implementations
- Shows mixed parameter usage patterns

## Backwards Compatibility

### Zero Breaking Changes
- All existing code continues to work unchanged
- Parameter names are optional additions
- Existing positional parameters function identically

### Migration Path
Developers can gradually adopt parameter names:

1. **Continue using existing code** - No changes required
2. **Add parameter names selectively** - Mix with positional parameters
3. **Full migration** - Convert all parameters to named for better maintainability

## Benefits Achieved

### 1. Meta WhatsApp API Compliance
- Full support for `parameter_name` field
- Compliant with latest API specifications
- Future-proof implementation

### 2. Developer Experience
- Fluent interface for easy parameter naming
- Self-documenting code through named parameters
- Reduced template maintenance errors

### 3. Template Management
- Named parameters make templates more readable
- Easier to identify parameter purposes
- Better error prevention in complex templates

### 4. Scalability
- Supports mixing named and positional parameters
- Easy migration path for existing projects
- Flexible implementation for various use cases

## Files Modified/Created

### Core Implementation
- `src/Component/Component.php` - Enhanced base class
- `src/Component/Text.php` - Added parameter name support
- `src/Component/Currency.php` - Added parameter name support
- `src/Component/DateTime.php` - Added parameter name support
- `src/Component/Image.php` - Added parameter name support
- `src/Component/Video.php` - Added parameter name support
- `src/Component/Document.php` - Added parameter name support
- `src/Component/Button.php` - Added parameter name support
- `src/Component/Location.php` - **NEW** - Location component with parameter name support
- `src/Component.php` - Added Location factory method

### Testing
- `tests/Component/ParameterNameTest.php` - **NEW** - Parameter name unit tests
- `tests/ParameterNameIntegrationTest.php` - **NEW** - Integration tests
- `tests/Component/LocationTest.php` - **NEW** - Location component tests
- `tests/ComponentTest.php` - Updated for Location component

### Documentation
- `README.md` - Enhanced with parameter name documentation
- `examples/ParameterNameUsageExamples.php` - **NEW** - Usage examples
- `summary.md` - **NEW** - This comprehensive summary

## Verification

### Test Results
```
PHPUnit 9.6.23 by Sebastian Bergmann and contributors.
Runtime: PHP 8.3.19

OK (47 tests, 78 assertions)
```

### Manual Verification
All components properly generate the expected API structure with parameter names when used, while maintaining full backwards compatibility for existing implementations.

---

## Conclusion

The `parameter_name` functionality has been successfully implemented across the entire Laravel WhatsApp notification package. The implementation:

✅ **Fully complies** with Meta WhatsApp Cloud API requirements  
✅ **Maintains 100% backwards compatibility**  
✅ **Supports all component types** including a new Location component  
✅ **Includes comprehensive testing** with 47 passing tests  
✅ **Provides clear documentation** and examples  
✅ **Offers flexible usage patterns** for different scenarios  

The package is now ready for production use with full Meta WhatsApp API parameter name support.
