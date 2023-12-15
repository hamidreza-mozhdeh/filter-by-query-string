# Make Laravel Eloquent Filterable by URL query strings

## About Filter by query string
With this package you can simply use Eloquent scopes to filter the model result by using URL query strings.
- It's safe
- Reusable scopes
- Easy to implement
- Customizable

## How to install
Simply run `composer require hamidreza-mozhdeh/filter-by-query-string`. It will add a `FilterByQueryString` trait to your project.

## How to use
- Add the trait to your models `use FilterByQueryString;`.
### Category Model:
```
  class Category extends Model
  {
    use CategoryScopesTrait;
    use FilterByQueryString;
  }
```
- In your controller simply pass the Form Request to the Model:
  - Example: `$categories = Category::filter($request);`
  - Please note you have to type hint the `Request` class.
  - #### Important: First of all is better to have a From Request for each your actions or methods (optional).
### CategoryController:
```
class CategoryController extends Controller
{
    public function index(CategoryRequest $request)
    {
        return Category::filter($request);
    }
```
### CategoryRequest:
```
class CategoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'min:3', 'max:254'], // A single filter

            // Use Associative example
            'date-between' => ['sometimes', 'array'], // A filter with two values
            'date-between.from' => ['sometimes', 'date'], // Associative array
            'date-between.to' => ['sometimes', 'date'], // Associative array
             
            // Or Indexed example
            'date-between' => ['sometimes', 'array'], // A filter with two values
            'date-between.0' => ['sometimes', 'date'], // Indexed array
            'date-between.1' => ['sometimes', 'date'], // Indexed array
        ];
    }
}
```
- Define your scopes to use the filters
  - It's better to have a directory and traits for your models in some where like `App\Models\Traits\Scopes\CategoryScopesTrait`.
### CategoryScopesTrait:
```
trait CategoryScopesTrait
{
    public function scopeName(Builder $query, string $name): Builder
    {
        return $query->where('name', 'like', "%{$name}%");
    }

    public function scopeDateBetween(Builder $query, string $from, string $to): Builder
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }
}
```
- To filter the categories' names for example `ossw` use: <br>
  `http://0.0.0.0/api/categories?name=ossw`
- To filter with dates use: <br>
`http://0.0.0.0/api/categories?date-between[]=2023-11-23&date-between[]=2023-11-25` <br>
  `http://0.0.0.0/api/categories?date-between[from]=2023-11-23&date-between[to]=2023-11-25`
## Customization
```
  $categories = Category::filter(
    request: $request,
    only: ['name'], // Only accept these two scopes.
    except: ['date-between'], // Do not accept the `scopeStatus` method.
    prefix: 'filters', // Add `filters` as array prefix.
    requestMethod: 'input' // The default get method is validated but you can choose different one.
  );
```
With prefix: `http://0.0.0.0/api/categories?filters[name]=ossw`