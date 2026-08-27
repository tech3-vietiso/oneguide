<?php

namespace Vietiso\OneGuide\Guide;

class InvitationStatus
{
    const PENDING = 1;  // Đã mời, hướng dẫn viên chưa phản hồi
    const ACCEPTED = 2; // Hướng dẫn viên đã nhận tour
    const DECLINED = 3; // Hướng dẫn viên đã từ chối
}
