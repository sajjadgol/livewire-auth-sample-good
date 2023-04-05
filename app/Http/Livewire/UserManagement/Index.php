<?php

namespace App\Http\Livewire\UserManagement;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Carbon;
use App\Http\DataTable\WithSorting;
use App\Http\DataTable\WithCachedRows;
use App\Http\DataTable\WithBulkActions;
use App\Http\DataTable\WithPerPagePagination;
use Spatie\Permission\Models\Role;
use App\Http\DataTable\WithSingleAction;
use App\Http\DataTable\Column;
class Index extends Component
{
    use WithPerPagePagination, // Added perPage
        Column,
        WithSorting, // Added Sorting
        WithBulkActions, // Bulk actions
        WithCachedRows, // Improved return  response
        WithSingleAction; // delete on row item

    // Apply Filters
    public $filters = [
        "search" => "",
        "status" => "",
        "role" => "",
        "from_date" => "",
        "to_date" => "",
    ];

    // Event listeners are registered in the $listeners property of your Livewire components.
    protected $listeners = [
        "refreshTransactions" => '$refresh',
        "deleteSelected",
        "confirm",
    ];

    /* Apply bootstrap layout in pagination */
    protected $paginationTheme = "bootstrap";

    public $roles;
    public $account_status = "";

    /**
     * Generic string-based column, attributes assigned
     *
     * @return array() response()
     */
    public function columns(): array
    {
        return [
            Column::field([
                "label" => __('user::user.Photo'),
                "field" => "profile_photo",
                "sortable" => true,
                "direction" => true,
                "hidden" => true,
            ]),
            Column::field([
                "label" => __('user::user.Name'),
                "field" => "name",
                "sortable" => true,
                "direction" => true,
            ]),
            Column::field([
                "label" => __('user::user.Phone'),
                "field" => "phone",
            ]),
            Column::field([
                "label" => __('user::user.Status'),
                "field" => "status",
            ]),
            Column::field([
                "label" => __('user::user.Role'),
                "field" => "role",
            ]),
            Column::field([
                "label" => __('user::user.Creation Date'),
                "field" => "created_at",
            ]),
        ];
    }

    /**
     * The loadData action will be run immediately after the Livewire component renders on the page
     *
     * @return void()
     */
    public function init()
    {
        $this->loadData = true;
    }

    public function mount($role = "")
    {
        $this->filters["role"] = $role;
        $this->roles = Role::where("status", 1)->get(["id", "name"]);
    }

    /**
     * Pass it to swal:destroyMultiple key of the alert configuration.
     *
     * @return void()
     */
    public function destroyMultiple()
    {
        $this->dispatchBrowserEvent("swal:destroyMultiple", [
            "action" => "deleteSelected",
            "type" => "warning",
            "confirmButtonText" => __('user::user.Yes, delete it!'),
            "cancelButtonText" => __('user::user.No, cancel!'),
            "message" => __('user::user.Are you sure?'),
            "text" => __(
                'user::user.If deleted, you will not be able to recover this imaginary file!'
            ),
        ]);
    }

    /**
     * Remove the selected blog from the storage.
     *
     * @return void()
     */
    public function deleteSelected()
    {
        $deleteCount = $this->selectedRowsQuery->count();

        $this->selectedRowsQuery->delete();
        $this->dispatchBrowserEvent("alert", [
            "type" => "success",
            "message" =>
            __('user::user.User Delete Successfully!') . " -: " . $deleteCount,
        ]);
    }

    /**
     * Clear the filter form and revert the results to default
     *
     * @return void()
     */
    public function resetFilters()
    {
        $this->reset("filters");
    }

    /**
     * Return a array of  all of the 's users with filter.
     *
     * @return \Illuminate\Http\Response
     */
    public function getRowsQueryProperty()
    {
        $query = User::query()
            ->when(
                $this->filters["status"],
                fn($query, $status) => $query->where("status", $status)
            )
            ->when(
                $this->filters["search"],
                fn($query, $search) => $query->where(
                    "name",
                    "like",
                    "%" . $search . "%"
                )
            )
            ->when(
                $this->filters["from_date"],
                fn($query, $date) => $query->where(
                    "created_at",
                    ">=",
                    Carbon::parse($date)
                )
            )
            ->when(
                $this->filters["to_date"],
                fn($query, $date) => $query->where(
                    "created_at",
                    "<=",
                    Carbon::parse($date)
                )
            )
            ->when(
                $this->filters["role"],
                fn($query, $role) => $query->whereHas("roles", function (
                    $query
                ) use ($role) {
                    $query->where("name", "=", ucfirst($role));
                })
            )
            ->with(["roles"]);

        return $this->applySorting($query);
    }

    /**
     * Store query result in cache
     * Return a list of cache users of the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function getRowsProperty()
    {
        return $this->cache(function () {
            return $this->applyPagination($this->rowsQuery);
        });
    }

    /**
     * Remove the specified resource from storage.
     * @param  int  $this->dltid
     * @return \Illuminate\Http\Response
     */
    public function remove()
    {
        return (clone $this->rowsQuery)->whereId($this->dltid)->delete();
    }

    /**
     * Show a list of all of the application's users.
     * @return \Illuminate\Http\Response
     */
    public function render()
    {
        return view("livewire.user-management.index", [
            "users" => $this->rows,
        ]);
    }
}