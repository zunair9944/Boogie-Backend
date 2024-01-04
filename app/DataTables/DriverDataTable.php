<?php

namespace App\DataTables;

use App\Models\User;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use DateTime;
use App\Traits\DataTableTrait;

class DriverDataTable extends DataTable
{
    use DataTableTrait;
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->editColumn('miles_travel', function($query) {
                return $query->driverRideRequestDetail->sum('distance');
            })
            ->editColumn('hours_travel', function($query) {
                        $totalHours = 0;
                        $totalMinutes = 0;
                        $totalSeconds = 0;
                    
                            foreach ($query->driverRideRequestDetail as $detail) {
                                
                                $startTime = DateTime::createFromFormat('h:i:a', $detail->start_time);
                                $endTime = DateTime::createFromFormat('h:i:a', $detail->end_time);
                                // Calculate the time difference
                               if($startTime && $endTime)
                                {
                                    $timeDifference = $startTime->diff($endTime);
                                    $totalHours += $timeDifference->h;
                                    $totalMinutes += $timeDifference->i;
                                    $totalSeconds += $timeDifference->s;
                                }
                                else
                               {
                                    $totalHours += 0;
                                    $totalMinutes += 0;
                                    $totalSeconds += 0;
                               }
                    
                                // Add the time difference components to the total
                               
                            }
                    
                        // Convert excess minutes and seconds to hours if necessary
                        $totalHours += floor($totalMinutes / 60);
                        $totalMinutes = $totalMinutes % 60;
                        $totalHours += floor($totalSeconds / 3600);
                        $totalSeconds = $totalSeconds % 60;
                        return str_pad($totalHours, 2, '0', STR_PAD_LEFT).':'.str_pad($totalMinutes, 2, '0', STR_PAD_LEFT);
            })
            ->editColumn('status', function($query) {
                $status = 'warning';
                switch ($query->status) {
                    case 'active':
                        $status = 'primary';
                        break;
                    case 'inactive':
                        $status = 'danger';
                        break;
                    case 'banned':
                        $status = 'dark';
                        break;
                }
                return '<span class="text-capitalize badge bg-'.$status.'">'.$query->status.'</span>';
            })
            ->editColumn('is_verified_driver', function($driver) {

                $is_verified_driver = $driver->is_verified_driver;
                if( $is_verified_driver == '0'){
                    $status = '<span class="badge badge-warning">'.__('message.unverified').'</span>';
                }else{
                    $status = '<span class="badge badge-success">'.__('message.verified').'</span>';
                }
                return $status;
            })
            ->editColumn('created_at', function($query) {
                return date('Y/m/d',strtotime($query->created_at));
            })

            ->addIndexColumn()
            ->addColumn('action', 'driver.action')
            ->rawColumns(['action','status','distance_travel','hours_travel','is_verified_driver']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $model = User::with('driverRideRequestDetail')->where('user_type','driver');
        if(auth()->user()->hasRole('fleet')) {
            $model->where('fleet_id', auth()->user()->id);
        }
        return $this->applyScopes($model);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            Column::make('DT_RowIndex')
                ->searchable(false)
                ->title(__('message.srno'))
                ->orderable(false)
                ->width(60),
            Column::make('display_name')->title( __('message.name') ),
            Column::make('contact_number'),
            Column::make('address'),
            Column::make('status'),
             Column::make('miles_travel'),
            Column::make('hours_travel'),
            Column::make('is_verified_driver')->title( __('message.is_verify') ),
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->width(60)
                  ->addClass('text-center'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'driver_' . date('YmdHis');
    }
}
