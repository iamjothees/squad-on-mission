
use App\Http\Controllers\Api\TimerController;

Route::get('/timers/{timer}', [TimerController::class, 'show']);
Route::post('/timers/{timer}', [TimerController::class, 'update']);
