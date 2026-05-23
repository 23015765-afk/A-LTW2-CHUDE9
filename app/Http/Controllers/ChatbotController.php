<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\Post;
use App\Models\Category;

class ChatbotController extends Controller
{
    /**
     * POST /chatbot — Xu ly tin nhan, khong can API key
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|min:1|max:500',
        ]);

        $message = trim($request->input('message'));

        // Rate limiting
        $ip       = $request->ip();
        $cacheKey = 'chatbot_rate_' . md5($ip);
        $count    = Cache::get($cacheKey, 0);
        if ($count >= 30) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn gửi quá nhiều tin nhắn. Vui lòng thử lại sau 1 phút! ⏳',
                'posts'   => [],
            ], 429);
        }
        Cache::put($cacheKey, $count + 1, 60);

        $result = $this->processMessage($message);

        return response()->json([
            'success' => true,
            'message' => $result['text'],
            'posts'   => $result['posts'],
        ]);
    }

    /**
     * Xu ly tin nhan — logic chatbot theo tu khoa
     */
    private function processMessage(string $message): array
    {
        $msg = mb_strtolower($message, 'UTF-8');

        // 1. Chao hoi
        if ($this->contains($msg, ['xin chào', 'chào', 'hello', 'hi', 'hey', 'alo'])) {
            return ['text' => $this->greet(), 'posts' => []];
        }
        // 2. Cam on
        if ($this->contains($msg, ['cảm ơn', 'cám ơn', 'thanks', 'thank you', 'tks'])) {
            return ['text' => 'Không có gì! Tôi luôn sẵn sàng hỗ trợ bạn. Bạn cần tư vấn thêm gì không? 😊', 'posts' => []];
        }
        // 3. Tam biet
        if ($this->contains($msg, ['tạm biệt', 'bye', 'goodbye', 'hẹn gặp lại'])) {
            return ['text' => 'Tạm biệt! Chúc bạn có chuyến du lịch thật vui vẻ! ✈️🌏', 'posts' => []];
        }
        // 4. Dia diem
        $loc = $this->searchByLocation($msg);
        if ($loc) return $loc;
        // 5. Danh muc
        $cat = $this->searchByCategory($msg);
        if ($cat) return $cat;
        // 6. Chi phi
        if ($this->contains($msg, ['chi phí', 'bao nhiêu tiền', 'giá', 'ngân sách', 'tiết kiệm', 'rẻ', 'budget'])) {
            return $this->budgetAdvice($msg);
        }
        // 7. Thoi diem
        if ($this->contains($msg, ['khi nào', 'tháng mấy', 'mùa nào', 'thời điểm', 'thời tiết', 'mùa'])) {
            return ['text' => $this->seasonAdvice($msg), 'posts' => []];
        }
        // 8. Am thuc
        if ($this->contains($msg, ['ăn gì', 'món ăn', 'đặc sản', 'ẩm thực', 'quán ăn', 'nhà hàng', 'food'])) {
            return $this->foodAdvice($msg);
        }
        // 9. Khach san
        if ($this->contains($msg, ['khách sạn', 'homestay', 'resort', 'ở đâu', 'lưu trú', 'phòng', 'hotel'])) {
            return $this->hotelAdvice($msg);
        }
        // 10. Di chuyen
        if ($this->contains($msg, ['đi bằng gì', 'phương tiện', 'xe', 'máy bay', 'tàu', 'di chuyển', 'đường đi'])) {
            return ['text' => $this->transportAdvice($msg), 'posts' => []];
        }
        // 11. Website
        if ($this->contains($msg, ['website', 'trang web', 'travelguide', 'bài viết', 'danh mục', 'tìm kiếm'])) {
            return ['text' => $this->websiteInfo(), 'posts' => []];
        }
        // 12. Tim tu do
        $search = $this->searchPosts($message);
        if ($search) return $search;

        // 13. Fallback
        return ['text' => $this->fallback($message), 'posts' => []];
    }

    // =========================================================
    // CAC HAM TRA LOI THEO CHU DE
    // =========================================================

    private function greet(): string
    {
        $totalPosts = Post::where('status', 'published')->count();
        $totalCats  = Category::count();
        return "Xin chào! Tôi là **TravelBot** 🌏\n"
             . "Tôi có thể giúp bạn:\n"
             . "• 🗺️ Tư vấn địa điểm du lịch\n"
             . "• 🍜 Gợi ý ẩm thực đặc sản\n"
             . "• 🏨 Tìm khách sạn, homestay\n"
             . "• 💰 Lập kế hoạch chi phí\n"
             . "• 📖 Tìm bài viết (hiện có **{$totalPosts} bài** trong **{$totalCats} danh mục**)\n\n"
             . "Bạn muốn khám phá đâu hôm nay? ✈️";
    }

    private function searchByLocation(string $msg): ?array
    {
        $locations = [
            'đà nẵng'     => ['Da Nang', 'Đà Nẵng'],
            'hà nội'      => ['Ha Noi', 'Hà Nội'],
            'hội an'      => ['Hoi An', 'Hội An'],
            'phú quốc'    => ['Phu Quoc', 'Phú Quốc'],
            'đà lạt'      => ['Da Lat', 'Đà Lạt'],
            'nha trang'   => ['Nha Trang'],
            'sapa'        => ['Sapa', 'Sa Pa'],
            'hạ long'     => ['Ha Long', 'Hạ Long'],
            'huế'         => ['Hue', 'Huế'],
            'hà giang'    => ['Ha Giang', 'Hà Giang'],
            'ninh bình'   => ['Ninh Binh', 'Ninh Bình'],
            'sài gòn'     => ['Sai Gon', 'TP. Hồ Chí Minh', 'HCM'],
            'hồ chí minh' => ['Ho Chi Minh', 'TP.HCM'],
            'mũi né'      => ['Mui Ne', 'Mũi Né'],
            'phong nha'   => ['Phong Nha'],
            'cần thơ'     => ['Can Tho', 'Cần Thơ'],
        ];

        foreach ($locations as $keyword => $searchTerms) {
            if (mb_strpos($msg, $keyword, 0, 'UTF-8') !== false) {
                $posts = Post::where('status', 'published')
                    ->where(function ($q) use ($searchTerms) {
                        foreach ($searchTerms as $term) {
                            $q->orWhere('location', 'like', "%{$term}%")
                              ->orWhere('title', 'like', "%{$term}%");
                        }
                    })
                    ->orderBy('views_count', 'desc')
                    ->take(3)
                    ->get();

                $locationName = ucwords($keyword);
                if ($posts->isEmpty()) {
                    return [
                        'text'  => "Tôi chưa có bài viết về **{$locationName}** nhưng đây là điểm đến tuyệt vời! 🌟\nBạn có thể tìm kiếm thêm tại trang Bài viết nhé.",
                        'posts' => [],
                    ];
                }

                $text = "Tôi tìm thấy **{$posts->count()} bài viết** về **{$locationName}** 📍\n";
                $postData = [];
                foreach ($posts as $post) {
                    $excerpt = $post->excerpt ? mb_substr(strip_tags($post->excerpt), 0, 70, 'UTF-8') . '...' : '';
                    $postData[] = [
                        'title'      => $post->title,
                        'slug'       => $post->slug,
                        'location'   => $post->location,
                        'views'      => number_format($post->views_count),
                        'excerpt'    => $excerpt,
                    ];
                }
                return ['text' => $text, 'posts' => $postData];
            }
        }
        return null;
    }

    private function searchByCategory(string $msg): ?array
    {
        $categoryMap = [
            'ẩm thực'     => ['ăn', 'món', 'food', 'ẩm thực', 'đặc sản', 'quán'],
            'điểm đến'    => ['điểm đến', 'địa điểm', 'tham quan', 'du lịch', 'khám phá'],
            'checkin'     => ['checkin', 'check-in', 'sống ảo', 'chụp ảnh', 'instagrammable'],
            'kinh nghiệm' => ['kinh nghiệm', 'mẹo', 'tips', 'bí kíp', 'lưu ý'],
            'khách sạn'   => ['khách sạn', 'resort', 'homestay', 'lưu trú', 'ở đâu'],
            'cẩm nang'    => ['cẩm nang', 'hướng dẫn', 'guide', 'lịch trình'],
        ];

        foreach ($categoryMap as $catName => $keywords) {
            foreach ($keywords as $kw) {
                if (mb_strpos($msg, $kw, 0, 'UTF-8') !== false) {
                    $category = Category::where('name', 'like', "%{$catName}%")->first();
                    if (!$category) continue;

                    $posts = Post::where('status', 'published')
                        ->where('category_id', $category->id)
                        ->orderBy('views_count', 'desc')
                        ->take(3)
                        ->get();

                    if ($posts->isEmpty()) {
                        return ['text' => "Danh mục **{$catName}** hiện chưa có bài viết. Hãy quay lại sau nhé! 😊", 'posts' => []];
                    }

                    $text = "📂 Danh mục **{$catName}** — Top bài viết nổi bật:\n";
                    $postData = [];
                    foreach ($posts as $post) {
                        $postData[] = [
                            'title'    => $post->title,
                            'slug'     => $post->slug,
                            'location' => $post->location,
                            'views'    => number_format($post->views_count),
                            'excerpt'  => '',
                        ];
                    }
                    return ['text' => $text, 'posts' => $postData];
                }
            }
        }
        return null;
    }

    private function budgetAdvice(string $msg): array
    {
        $posts = Post::where('status', 'published')
            ->where(function ($q) {
                $q->where('title', 'like', '%tiết kiệm%')
                  ->orWhere('title', 'like', '%chi phí%')
                  ->orWhere('title', 'like', '%budget%')
                  ->orWhere('title', 'like', '%rẻ%');
            })
            ->orderBy('views_count', 'desc')
            ->take(2)
            ->get();

        $text = "💰 **Mẹo du lịch tiết kiệm:**\n\n"
              . "• ✈️ Đặt vé máy bay trước 1-2 tháng, thứ 3-4 thường rẻ hơn\n"
              . "• 🏨 Chọn homestay hoặc hostel thay vì khách sạn (tiết kiệm 50-70%)\n"
              . "• 🍜 Ăn ở quán địa phương, tránh nhà hàng gần khu du lịch\n"
              . "• 🛵 Thuê xe máy thay vì taxi (150-200k/ngày)\n"
              . "• 📅 Đi vào mùa thấp điểm (tháng 3-4, 9-10)\n";

        $postData = [];
        foreach ($posts as $post) {
            $postData[] = ['title' => $post->title, 'slug' => $post->slug, 'location' => $post->location, 'views' => number_format($post->views_count), 'excerpt' => ''];
        }
        return ['text' => $text, 'posts' => $postData];
    }

    private function seasonAdvice(string $msg): string
    {
        $seasons = [
            'đà nẵng'  => "Đà Nẵng đẹp nhất tháng 3-8 (mùa khô). Tránh tháng 9-11 (mưa bão).",
            'hà nội'   => "Hà Nội đẹp nhất tháng 9-11 (mùa thu) and tháng 3-4 (mùa xuân).",
            'phú quốc' => "Phú Quốc đẹp nhất tháng 11-4 (mùa khô). Tránh tháng 6-9 (mưa nhiều).",
            'đà lạt'   => "Đà Lạt đẹp quanh năm. Tháng 11-12 có hoa dã quỳ vàng rực.",
            'sapa'     => "Sapa đẹp nhất tháng 9-10 (lúa chín vàng) and tháng 3-4 (hoa đào).",
            'hạ long'  => "Hạ Long đẹp nhất tháng 4-8. Tránh tháng 11-3 (sương mù, lạnh).",
        ];

        foreach ($seasons as $location => $advice) {
            if (mb_strpos($msg, $location, 0, 'UTF-8') !== false) {
                return "🗓️ **Thời điểm lý tưởng — " . ucwords($location) . ":**\n\n"
                     . $advice . "\n\n"
                     . "💡 Tip: Đặt phòng trước ít nhất 2 tuần vào mùa cao điểm!";
            }
        }

        return "🗓️ **Thời điểm du lịch tốt nhất theo vùng:**\n\n"
             . "• 🌞 **Miền Bắc** (Hà Nội, Sapa): Tháng 9-11 và 3-4\n"
             . "• ☀️ **Miền Trung** (Đà Nẵng, Huế): Tháng 3-8\n"
             . "• 🌴 **Miền Nam** (Phú Quốc, Cần Thơ): Tháng 11-4\n"
             . "• 🏔️ **Tây Nguyên** (Đà Lạt): Quanh năm đều đẹp\n\n"
             . "Bạn muốn biết thêm về địa điểm cụ thể nào không?";
    }

    private function foodAdvice(string $msg): array
    {
        $foodByLocation = [
            'hà nội'   => "Phở Bát Đàn, Bún chả Obama, Bánh cuốn Thanh Vân, Chả cá Lã Vọng, Kem Tràng Tiền",
            'đà nẵng'  => "Mì Quảng, Bánh tráng cuốn thịt heo, Bún mắm nêm, Bánh xèo, Hải sản tươi sống",
            'hội an'   => "Cao lầu, Mì Quảng, Bánh mì Phượng, Cơm gà Bà Buội, Chè bắp",
            'huế'      => "Bún bò Huế, Bánh bèo, Cơm hến, Bánh khoái, Chè Huế",
            'sài gòn'  => "Phở Hòa Pasteur, Bánh mì Huỳnh Hoa, Cơm tấm, Hủ tiếu Nam Vang",
            'phú quốc' => "Hải sản tươi sống, Nước mắm Phú Quốc, Nhum biển, Gỏi cá trích",
        ];

        foreach ($foodByLocation as $location => $foods) {
            if (mb_strpos($msg, $location, 0, 'UTF-8') !== false) {
                return [
                    'text'  => "🍜 **Ẩm thực " . ucwords($location) . " không thể bỏ qua:**\n\n" . $foods . "\n\n💡 Tip: Ăn ở chợ địa phương để thưởng thức đúng vị!",
                    'posts' => [],
                ];
            }
        }

        $posts = Post::where('status', 'published')
            ->whereHas('category', function ($q) { $q->where('name', 'like', '%ẩm thực%'); })
            ->orderBy('views_count', 'desc')->take(3)->get();

        $text = "🍜 **Ẩm thực Việt Nam nổi tiếng:**\n\n"
              . "• Phở, Bún bò, Bánh mì — món ăn đường phố huyền thoại\n"
              . "• Mỗi vùng có đặc sản riêng, hãy thử đồ ăn địa phương!\n";

        $postData = [];
        foreach ($posts as $post) {
            $postData[] = ['title' => $post->title, 'slug' => $post->slug, 'location' => $post->location, 'views' => number_format($post->views_count), 'excerpt' => ''];
        }
        return ['text' => $text, 'posts' => $postData];
    }

    private function hotelAdvice(string $msg): array
    {
        $posts = Post::where('status', 'published')
            ->whereHas('category', function ($q) {
                $q->where('name', 'like', '%khách sạn%')->orWhere('name', 'like', '%checkin%');
            })
            ->orderBy('views_count', 'desc')->take(3)->get();

        $text = "🏨 **Lời khuyên chọn chỗ ở:**\n\n"
              . "• 💰 **Tiết kiệm**: Hostel, Homestay (200-400k/đêm)\n"
              . "• 🌟 **Tầm trung**: Khách sạn 3 sao (400-800k/đêm)\n"
              . "• 👑 **Cao cấp**: Resort, Boutique Hotel (1-5 triệu/đêm)\n\n"
              . "💡 Đặt qua Agoda, Booking.com để so sánh giá tốt nhất!\n";

        $postData = [];
        foreach ($posts as $post) {
            $postData[] = ['title' => $post->title, 'slug' => $post->slug, 'location' => $post->location, 'views' => number_format($post->views_count), 'excerpt' => ''];
        }
        return ['text' => $text, 'posts' => $postData];
    }

    private function transportAdvice(string $msg): string
    {
        return "🚗 **Phương tiện di chuyển phổ biến:**\n\n"
             . "✈️ **Máy bay**: Nhanh nhất, đặt sớm giá rẻ (Vietjet, Bamboo, Vietnam Airlines)\n"
             . "🚂 **Tàu hỏa**: Ngắm cảnh đẹp, phù hợp Hà Nội-Đà Nẵng-Sài Gòn\n"
             . "🚌 **Xe khách**: Rẻ nhất, many tuyến, limousine thoải mái hơn\n"
             . "🛵 **Xe máy**: Tự do khám phá, thuê 150-200k/ngày\n"
             . "🚕 **Grab**: Tiện lợi trong thành phố, giá cố định\n\n"
             . "💡 **Tip**: Đặt vé máy bay thứ 3-4 thường rẻ hơn 20-30%!";
    }

    private function websiteInfo(): string
    {
        $totalPosts = Post::where('status', 'published')->count();
        $categories = Category::withCount(['posts' => function ($q) {
            $q->where('status', 'published');
        }])->get();

        $catList = '';
        foreach ($categories as $cat) {
            $catList .= "• **{$cat->name}**: {$cat->posts_count} bài\n";
        }

        return "🌐 **Về TravelGuide:**\n\n"
             . "Chúng tôi có **{$totalPosts} bài viết** chia sẻ kinh nghiệm du lịch Việt Nam.\n\n"
             . "📂 **Danh mục:**\n{$catList}\n"
             . "🔍 Bạn có thể tìm kiếm bài viết theo:\n"
             . "• Địa điểm (Đà Nẵng, Hà Nội, Phú Quốc...)\n"
             . "• Danh mục (Ẩm thực, Điểm đến, Khách sạn...)\n"
             . "• Từ khóa bất kỳ\n\n"
             . "Bạn muốn tìm gì? 😊";
    }

    private function searchPosts(string $message): ?array
    {
        $keywords = preg_split('/\s+/', trim($message));
        $keywords = array_filter($keywords, function ($k) {
            return mb_strlen($k, 'UTF-8') >= 3;
        });

        if (empty($keywords)) return null;

        $query = Post::where('status', 'published');
        foreach ($keywords as $kw) {
            $query->where(function ($q) use ($kw) {
                $q->where('title', 'like', "%{$kw}%")
                  ->orWhere('excerpt', 'like', "%{$kw}%")
                  ->orWhere('location', 'like', "%{$kw}%");
            });
        }

        $posts = $query->orderBy('views_count', 'desc')->take(3)->get();

        if ($posts->isEmpty()) return null;

        $reply = "🔍 Tôi tìm thấy **{$posts->count()} bài viết** liên quan:\n\n";
        $postData = [];

        foreach ($posts as $i => $post) {
            $reply .= ($i + 1) . ". **{$post->title}**";
            if ($post->location) $reply .= " 📍 {$post->location}";
            $reply .= "\n   👁️ " . number_format($post->views_count) . " lượt xem\n\n";

            $postData[] = [
                'title'    => $post->title,
                'slug'     => $post->slug,
                'location' => $post->location,
                'views'    => number_format($post->views_count),
                'excerpt'  => '',
            ];
        }
        $reply .= "👉 Xem thêm tại trang **Bài viết**!";

        return [
            'text'  => $reply,
            'posts' => $postData
        ];
    }

    private function fallback(string $message): string
    {
        $suggestions = [
            '🏖️ Biển đẹp nhất Việt Nam',
            '🍜 Ẩm thực Hà Nội',
            '💰 Du lịch tiết kiệm',
            '🏔️ Trekking Sapa',
            '🏨 Khách sạn Đà Nẵng',
            '📅 Thời điểm đi Phú Quốc',
        ];

        $randomSuggestions = array_slice($suggestions, 0, 3);
        $suggestionText = implode(', ', $randomSuggestions);

        return "Xin lỗi, tôi chưa hiểu câu hỏi của bạn 😅\n\n"
             . "Bạn có thể hỏi tôi về:\n"
             . "• 🗺️ Địa điểm du lịch (Đà Nẵng, Hà Nội, Phú Quốc...)\n"
             . "• 🍜 Ẩm thực đặc sản từng vùng\n"
             . "• 🏨 Khách sạn, homestay\n"
             . "• 💰 Chi phí, ngân sách du lịch\n"
             . "• 📅 Thời điểm đi đẹp nhất\n"
             . "• 🚗 Phương tiện di chuyển\n\n"
             . "Ví dụ: *\"Du lịch Đà Nẵng cần bao nhiêu tiền?\"*";
    }

    private function contains(string $text, array $keywords): bool
    {
        foreach ($keywords as $kw) {
            if (mb_strpos($text, $kw, 0, 'UTF-8') !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * GET /chatbot/test — kiem tra chatbot (local only)
     */
    public function test()
    {
        if (!app()->isLocal()) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        return response()->json([
            'status'       => 'OK - Chatbot tu dong san sang (khong can API key)',
            'total_posts'  => Post::where('status', 'published')->count(),
            'total_cats'   => Category::count(),
            'mode'         => 'keyword-based (no AI API required)',
        ]);
    }
}
